<?php

class SiteglossaryEditForm extends MailForm {
	function SiteglossaryEditForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/siteglossary/forms/edit/settings.php');

		global $cgi;

		page_title (intl_get ('Editing Glossary Term') . ': ' . $cgi->_key);

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

		$res = db_single ('select * from siteglossary_term where word = ?', $cgi->_key);
		foreach (get_object_vars ($res) as $k => $v) {
			$this->widgets[$k]->setValue ($v);
		}
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$rex = new Rex ('siteglossary_term');

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

		unset ($vals['section']);
		unset ($vals['submit_button']);

		$method = $rex->determineAction ($key);
		if (! $method) {
			die ($rex->error);
		}
		$res = $rex->{$method} ($key, $vals, $changelog);

		if (! $res) {
			die ($rex->error);
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