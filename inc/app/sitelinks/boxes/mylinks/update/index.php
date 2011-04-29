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

if (! session_valid ()) {
	global $cgi;
	page_title (intl_get ('You must be logged in to continue'));
	echo template_simple ('not_registered.spt', $cgi);
	return;
}

loader_import ('saf.MailForm');
loader_import ('saf.Date.Calendar.Simple');
loader_import ('saf.Date');

class SitelinksMylinksSubmitForm extends MailForm {
	function SitelinksMylinksSubmitForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitelinks/boxes/mylinks/update/settings.php');
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

		global $cgi;

		loader_import ('cms.Versioning.Rex');
		$rex = new Rex ('sitelinks_item');
		$item = $rex->getCurrent ($cgi->id);

		foreach ((array) $item as $k => $v) {
			if (isset ($this->widgets[$k])) {
				$this->widgets[$k]->setDefault ($v);
			}
		}

		loader_import ('saf.Database.PropertySet');
		$ps = new PropertySet ('sitelinks_item', $cgi->id);
		foreach ($ps->get () as $k => $v) {
			if (isset ($this->widgets[$k])) {
				$this->widgets[$k]->setDefault ($v);
			}
		}
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$rex = new Rex ('sitelinks_item');

		$action = $rex->determineAction ($vals['id'], 'draft');

//START: SEMIAS. #192 Test all config files for multilingual dates.
//-----------------------------------------------
//		$res = $rex->{$action} (
//			$vals['id'],
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
        $res = $rex->{$action} (
			$vals['id'],
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
//END: SEMIAS.

		if (! $res) {
			die ($rex->error);
		}

		loader_import ('saf.Database.PropertySet');

		$ps = new PropertySet ('sitelinks_item', $vals['id']);

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

page_title (intl_get ('Update') . ' ' . appconf ('sitelinks_name_singular'));
$form = new SitelinksMylinksSubmitForm;
echo $form->run ();

?>