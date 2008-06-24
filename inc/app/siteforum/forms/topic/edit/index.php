<?php

class SiteforumTopicEditForm extends MailForm {
	function SiteforumTopicEditForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/siteforum/forms/topic/edit/settings.php');

		global $cgi;

		page_title (intl_get ('Editing Topic') . ': ' . $cgi->id);

		loader_import ('siteforum.Topic');
		$t = new SiteForum_Topic;
		$topic = $t->get ($cgi->id);
		if (! $topic) {
			header ('Location: ' . site_prefix () . '/index/siteforum-app');
			exit;
		}
		unset ($topic->sitellite_owner);
		unset ($topic->sitellite_team);
		foreach (get_object_vars ($topic) as $k => $v) {
			$this->widgets[$k]->setValue ($v);
		}
	}

	function onSubmit ($vals) {
		loader_import ('siteforum.Topic');
		loader_import ('siteforum.Post');

		$t = new SiteForum_Topic;

		$t->modify (
			$vals['id'],
			array (
				'name' => $vals['name'],
				'description' => $vals['description'],
			'sitellite_access' => $vals['sitellite_access'],
			'sitellite_status' => $vals['sitellite_status'],
			)
		);

		$p = new SiteForum_Post;

		$p->modify (
			array (
				'topic_id' => $vals['id'],
			),
			array (
				'sitellite_access' => $vals['sitellite_access'],
				'sitellite_status' => $vals['sitellite_status'],
			)
		);

		header ('Location: ' . site_prefix () . '/index/siteforum-app');
		exit;
	}
}

if (appconf ('template')) {
	page_template (appconf ('template'));
}

?>
