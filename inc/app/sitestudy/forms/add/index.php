<?php

class SitestudyAddForm extends MailForm {
	function SitestudyAddForm () {
		parent::MailForm ();

		global $page, $cgi;

		$this->parseSettings ('inc/app/sitestudy/forms/add/settings.php');

		page_title (intl_get ('Adding Case Study'));

		loader_import ('ext.phpsniff');

		$sniffer = new phpSniff;
		$this->_browser = $sniffer->property ('browser');

		// include formhelp, edit panel init, and cancel handler
		page_add_script (site_prefix () . '/js/formhelp.js');
		page_add_script (CMS_JS_FORMHELP_INIT);
		page_onload ('cms_init_edit_panels ()');
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
		header ('Location: ' . site_prefix () . '/index/sitestudy-app/case.' . $res);
		exit;
	}
}

?>