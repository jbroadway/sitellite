<?php

class SiteglossaryAddForm extends MailForm {
	function SiteglossaryAddForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/siteglossary/forms/add/settings.php');

		page_title (intl_get ('Adding Glossary Term'));

		page_add_script ('
			function cms_cancel (f) {
				if (arguments.length == 0) {
					window.location.href = "/index/cms-app";
				} else {
					if (f.elements["_return"] && f.elements["_return"].value.length > 0) {
						window.location.href = f.elements["_return"].value;
					} else {
						window.location.href = "/index/siteglossary-app";
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

		$rex = new Rex ('siteglossary_term');

		$collection = $vals['collection'];
		unset ($vals['collection']);
		if (empty ($collection)) {
			$collection = 'sitellite_page';
		}

		$return = $vals['_return'];
		unset ($vals['_return']);

		$changelog = $vals['changelog'];
		unset ($vals['changelog']);

		unset ($vals['section']);
		unset ($vals['submit_button']);

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
		header ('Location: ' . site_prefix () . '/index/siteglossary-app#' . $vals['word']);
		exit;
	}
}

?>