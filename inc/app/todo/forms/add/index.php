<?php

class TodoAddForm extends MailForm {
	function TodoAddForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/todo/forms/add/settings.php');
	}

	function onSubmit ($vals) {
		$list = explode ("\n", $vals['todo']);
		foreach ($list as $todo) {
			$todo = str_replace ('"', "'", trim ($todo));
			if (empty ($todo)) {
				continue;
			}
			db_execute (
				'insert into todo_list (id, todo, priority, project, person, done) values (null, ?, ?, ?, ?, ?)',
				$todo,
				$vals['priority'],
				$vals['proj'],
				$vals['pp'],
				'0000-00-00 00:00:00'
			);
		}
		header ('Location: /index/todo-app?pp=' . $vals['pp'] . '&proj=' . $vals['proj']);
		exit;
	}
}

?>
