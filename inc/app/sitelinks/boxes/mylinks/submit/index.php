<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #192 Test all config files for multilingual dates.

if (! appconf ('user_submissions')) {
	header ('Location: ' . site_prefix () . '/index/sitelinks-app');
	exit;
}

global $cgi;
loader_import ('saf.Date.Calendar.Simple');
loader_import ('saf.Date');

if (! session_valid ()) {
	page_title (intl_get ('You must be logged in to continue'));
	echo template_simple ('not_registered.spt', $cgi);
	return;
}

if (empty ($cgi->category)) {
	page_title (intl_get ('Category'));
	echo template_simple (
		'category_selector.spt',
		db_fetch_array ('select id from sitelinks_category')
	);
	return;
}

loader_import ('saf.MailForm');

class SitelinksMylinksSubmitForm extends MailForm {
	function SitelinksMylinksSubmitForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitelinks/boxes/mylinks/submit/settings.php');
		$w =& $this->widgets['submit_button'];
		unset ($this->widgets['submit_button']);

		$type = appconf ('sitelinks_default_type');
		if (is_array ($type)) {
			global $cgi;
			if (isset ($type[$cgi->category])) {
				$type = $type[$cgi->category];
			} elseif (isset ($type['default'])) {
				$type = $type['default'];
			} else {
				$type = 'default';
			}
		}
		$this->_sitelinks_type = $type;

		$this->_info = @ini_parse ('inc/app/sitelinks/conf/types/' . $type . '.php');
		unset ($this->_info['Type']);
		foreach ($this->_info as $k => $v) {
			$this->createWidget ($k, $v);
		}

		$this->widgets['submit_button'] =& $w;
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/sitelinks-mylinks-action\'; return false"';
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$rex = new Rex ('sitelinks_item');
//START: SEMIAS. #192 Test all config files for multilingual dates.
//-----------------------------------------------
//		$res = $rex->create (
//			array (
//				'title' => $vals['title'],
//				'url' => $vals['url'],
//				'user_id' => session_username (),
//				'category' => $vals['category'],
//				'ctype' => $this->_sitelinks_type,
//				'ts' => date ('Y-m-d H:i:s'),
//				'summary' => $vals['summary'],
//				'sitellite_status' => 'draft',
//				'sitellite_access' => 'public',
//			)
//		);
//-----------------------------------------------
        $res = $rex->create (
			array (
				'title' => $vals['title'],
				'url' => $vals['url'],
				'user_id' => session_username (),
				'category' => $vals['category'],
				'ctype' => $this->_sitelinks_type,
				'ts' => intl_date (date ('Y-m-d H:i:s'), 'shortcevdate'),
				'summary' => $vals['summary'],
				'sitellite_status' => 'draft',
				'sitellite_access' => 'public',
			)
		);
//END: SEMIAS
		if (! $res) {
			die ($rex->error);
		}

		loader_import ('saf.Database.PropertySet');

		$ps = new PropertySet ('sitelinks_item', $res);

		foreach (array_keys ($this->_info) as $k) {
			$r = $ps->set ($k, $vals[$k]);
			if (! $r) {
				die ($ps->error);
			}
		}

		if (appconf ('email_user_submissions')) {
			@mail (
				appconf ('email_user_submissions'),
				'User Submission Notice',
				template_simple ('submission_notice.spt', $vals)
			);
		}

		page_title (appconf ('sitelinks_name_singular') . ' ' . intl_get ('Submitted'));
		echo template_simple ('submission_received.spt');
	}
}

page_title (intl_get ('Add') . ' ' . appconf ('sitelinks_name_singular'));
$form = new SitelinksMylinksSubmitForm;
echo $form->run ();

?>