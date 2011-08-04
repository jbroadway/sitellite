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

class SitestudyAddForm extends MailForm {
	function SitestudyAddForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/sitestudy/forms/add/settings.php');

		global $page, $cgi;
		
		page_title (intl_get ('Adding Case Study'));

		loader_import ('ext.phpsniff');

		$sniffer = new phpSniff;
		$this->_browser = $sniffer->property ('browser');

		// include formhelp, edit panel init, and cancel handler
		page_add_script (site_prefix () . '/js/formhelp-compressed.js');
		page_add_script (CMS_JS_FORMHELP_INIT);

		page_add_script ('
			function cms_cancel (f) {
				onbeforeunload_form_submitted = true;
				if (arguments.length == 0) {
					window.location.href = "/index/cms-app";
				} else {
					if (f.elements["_return"] && f.elements["_return"].value.length > 0) {
						window.location.href = f.elements["_return"].value;
					} else {
						window.location.href = "/index/sitestudy-app";
					}
				}
				return false;
			}
		');

		// add cancel handler
		$this->widgets['submit_button']->buttons[0]->extra = 'onclick="onbeforeunload_form_submitted = true;"';
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="onbeforeunload_form_submitted = true;"';
		$this->widgets['submit_button']->buttons[2]->extra = 'onclick="return cms_cancel (this.form)"';
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$collection = $vals['collection'];
		unset ($vals['collection']);
		if (empty ($collection)) {
			$collection = 'sitellite_page';
		}

		$return = $vals['_return'];
		unset ($vals['_return']);

		$changelog = $vals['changelog'];
		unset ($vals['changelog']);

		$rex = new Rex ($collection);

		$continue = ($vals['submit_button'] == intl_get ('Save and continue'));
		unset ($vals['submit_button']);
		unset ($vals['edit-top']);
		unset ($vals['edit-middle']);
		unset ($vals['edit-middle2']);
		unset ($vals['edit-middle3']);
		unset ($vals['edit-bottom']);
		unset ($vals['solution_header']);

		$res = $rex->create ($vals, $changelog);

		if (isset ($vals[$rex->key])) {
			$key = $vals[$rex->key];
		} elseif (! is_bool ($res)) {
			$key = $res;
		} else {
			$key = 'Unknown';
		}

		if (! $res) {
			if (empty ($return)) {
				$return = site_prefix () . '/index/cms-browse-action?collection=sitestudy_item';
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
				'add',
				array (
					'collection' => $collection,
					'key' => $key,
					'data' => $vals,
					'changelog' => intl_get ('Item added.'),
					'message' => 'Collection: ' . $collection . ', Item: ' . $key,
				)
			);

			session_set ('sitellite_alert', intl_get ('Your item has been created.'));
			
			if ($continue) {
					header ('Location: ' . site_prefix () . '/cms-edit-form?_collection=' . $collection . '&_key=' . $key . '&_return=' . $return);
					exit;
            }
			
			if (! empty($return)) {
				header ('Location: ' . $return);
				exit;
			}
		}
		header ('Location: ' . site_prefix () . '/index/sitestudy-app/case.' . $res);
		exit;
	}
}

?>