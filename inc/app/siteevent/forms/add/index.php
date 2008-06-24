<?php

class SiteeventAddForm extends MailForm {
	function SiteeventAddForm () {
		parent::MailForm ();

		global $page, $cgi;

		$this->parseSettings ('inc/app/siteevent/forms/add/settings.php');

		page_title (intl_get ('Adding Event'));

		loader_import ('ext.phpsniff');

		$sniffer = new phpSniff;
		$this->_browser = $sniffer->property ('browser');

		// include formhelp, edit panel init, and cancel handler
		page_add_script (site_prefix () . '/js/formhelp-compressed.js');
		page_add_script (CMS_JS_FORMHELP_INIT);
		//page_onload ('cms_init_edit_panels ()');
		page_add_script ('
			function cms_cancel (f) {
				if (arguments.length == 0) {
					window.location.href = "/index/cms-app";
				} else {
					if (f.elements["_return"] && f.elements["_return"].value.length > 0) {
						window.location.href = f.elements["_return"].value;
					} else {
						window.location.href = "/index/news-app";
					}
				}
				return false;
			}
		');

		// add cancel handler
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="return cms_cancel (this.form)"';
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

		//$vals['sitellite_owner'] = session_username ();
		//$vals['sitellite_team'] = session_team ();
		unset ($vals['submit_button']);
		unset ($vals['tab1']);
		unset ($vals['tab2']);
		unset ($vals['tab3']);
		unset ($vals['tab-end']);
		unset ($vals['header_properties']);
		unset ($vals['header_contact']);
		unset ($vals['header_loc']);

		if ($vals['contact_url'] == 'http://') {
			$vals['contact_url'] = '';
		}
		if ($vals['loc_map'] == 'http://') {
			$vals['loc_map'] = '';
		}

		$res = $rex->create ($vals, $changelog);

		if (isset ($vals[$rex->key])) {
			$key = $vals[$rex->key];
		} elseif (! is_bool ($res)) {
			$key = $res;
		} else {
			$key = 'Unknown';
		}

		if (! $res) {
			die ($rex->error);
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

			if ($return) {
				header ('Location: ' . $return);
				exit;
			}
		}
		header ('Location: ' . site_prefix () . '/index/siteevent-app/id.' . $res);
		exit;
	}
}

?>