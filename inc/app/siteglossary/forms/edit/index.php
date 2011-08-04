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
// #174 CMS cancel.
//

global $cgi;

loader_import ('cms.Workflow.Lock');

lock_init ();

if (lock_exists ($cgi->_collection, $cgi->_key)) {
	page_title (intl_get ('Item Locked by Another User'));
	echo '<p><a href="javascript: history.go (-1)">' . intl_get ('Back') . '</a></p>';
	echo template_simple (LOCK_INFO_TEMPLATE, lock_info ($cgi->_collection, $cgi->_key));
	return;
} else {
	lock_add ($cgi->_collection, $cgi->_key);
}

class SiteglossaryEditForm extends MailForm {
	function SiteglossaryEditForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/siteglossary/forms/edit/settings.php');

		global $page, $cgi;

		page_title (intl_get ('Editing Glossary Term') . ': ' . $cgi->_key);
		
		loader_import ('ext.phpsniff');

		$sniffer = new phpSniff;
		$this->_browser = $sniffer->property ('browser');
		
		// include formhelp, edit panel init, and cancel handler
		page_add_script (site_prefix () . '/js/formhelp-compressed.js');
		page_add_script (CMS_JS_FORMHELP_INIT);

		page_add_script ('
			function cms_cancel_unlock (f, collection, key) {
				onbeforeunload_form_submitted = true;
				if (arguments.length == 0) {
					window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=' . site_prefix () . '/index/cms-app";
				} else {
					if (f.elements[\'_return\'] && f.elements[\'_return\'].value.length > 0) {
						window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=" + encodeURIComponent (f.elements[\'_return\'].value);
					} else {
						window.location.href = "' . site_prefix () . '/index/cms-unlock-action?collection=" + collection + "&key=" + key + "&return=' . site_prefix () . '/index/siteevent-app";
					}
				}
				return false;
			}
		');

		// add cancel handler
        $this->widgets['submit_button']->buttons[0]->extra = 'onclick="onbeforeunload_form_submitted = true;"';
        $this->widgets['submit_button']->buttons[1]->extra = 'onclick="onbeforeunload_form_submitted = true;"';
		$this->widgets['submit_button']->buttons[2]->extra = 'onclick="return cms_cancel_unlock (this.form, \'' . $cgi->_collection . '\', \'' . $cgi->_key . '\')"';
		
		// get copy from repository
		loader_import ('cms.Versioning.Rex');
		$rex = new Rex ($cgi->_collection);
		$_document = $rex->getCurrent ($cgi->_key);

		$res = db_single ('select * from siteglossary_term where word = ?', $cgi->_key);
		foreach (get_object_vars ($res) as $k => $v) {
			$this->widgets[$k]->setValue ($v);
		}
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$collection = $vals['_collection'];
		unset ($vals['_collection']);
		if (empty ($collection)) {
			$collection = 'sitellite_page';
		}
		
		$key = $vals['_key'];
		unset ($vals['_key']);
		
		$return = $vals['_return'];
		unset ($vals['_return']);

		$changelog = $vals['changelog'];
		unset ($vals['changelog']);
		
		$rex = new Rex ($collection);
		
		$continue = ($vals['submit_button'] == intl_get ('Save and continue'));
		unset ($vals['section']);
		unset ($vals['submit_button']);

		$method = $rex->determineAction ($key);
		if (! $method) {
			die ($rex->error);
		}
		$res = $rex->{$method} ($key, $vals, $changelog);
		
		// remove lock when editing is finished
		lock_remove ($collection, $key);
		
		if (! $res) {
			if (empty ($return)) {
				$return = site_prefix () . '/index/siteglossary-app#' . $vals['word'];
			}
			echo loader_box ('cms/error', array (
				'message' => $rex->error,
				'collection' => $collection,
				'key' => $key,
				'action' => $method,
				'data' => $vals,
				'changelog' => $changelog,
				'return' => $return,
			));
		} else {
			loader_import ('cms.Workflow');
			echo Workflow::trigger (
				'edit',
				array (
					'collection' => $collection,
					'key' => $key,
					'action' => $method,
					'data' => $vals,
					'changelog' => $changelog,
					'message' => 'Collection: ' . $collection . ', Item: ' . $key,
				)
			);

			session_set ('sitellite_alert', intl_get ('Your item has been saved.'));
			
			if ($continue) {
					header ('Location: ' . site_prefix () . '/cms-edit-form?_collection=' . $collection . '&_key=' . $key . '&_return=' . $return);
					exit;
			}
			if ($return) {
				header ('Location: ' . $return);
				exit;
			}
		}
		header ('Location: ' . site_prefix () . '/index/siteglossary-app#' . $vals['word']);
		exit;
	}
}

?>