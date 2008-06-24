<?php

class ProjectAddForm extends MailForm {
	function ProjectAddForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/timetracker/forms/project/add/settings.php');

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="history.go (-1); return false"';
	}

	function onSubmit ($vals) {
		$res = db_execute ('insert into timetracker_project
				(id, name, description)
			values
				(null, ?, ?)',
			$vals['name'],
			$vals['description']
		);
		if (! $res) {
			return '<p>Unknown error: ' . db_error () . '</p>';
		}
		header ('Location: ' . site_prefix () . '/index/timetracker-app/added.project');
		exit;
	}
}

page_title (intl_get ('TimeTracker') . ' - ' . intl_get ('Add Project'));
$form = new ProjectAddForm;
echo $form->run ();

?>