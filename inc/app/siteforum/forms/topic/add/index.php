<?php

class SiteforumTopicAddForm extends MailForm {
	function SiteforumTopicAddForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/siteforum/forms/topic/add/settings.php');

		page_title (intl_get ('Add Topic'));
	}

	function onSubmit ($vals) {
		loader_import ('siteforum.Topic');

		$t = new SiteForum_Topic;

		$t->add (array (
			'name' => $vals['name'],
			'description' => $vals['description'],
			'sitellite_access' => $vals['sitellite_access'],
			'sitellite_status' => $vals['sitellite_status'],
		));

		header ('Location: ' . site_prefix () . '/index/siteforum-app');
		exit;
	}
}

if (appconf ('template')) {
	page_template (appconf ('template'));
}

?>
