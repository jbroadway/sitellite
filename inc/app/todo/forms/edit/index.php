<?php

class TodoEditForm extends MailForm {
	function TodoEditForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/todo/forms/edit/settings.php');

		global $cgi;
		$f = db_single ('select * from todo_list where id = ?', $cgi->id);
		$this->widgets['todo']->setValue ($f->todo);
		$this->widgets['priority']->setValue ($f->priority);
		$this->widgets['project']->setValue ($f->project);
		$this->widgets['person']->setValue ($f->person);
	}

	function onSubmit ($vals) {
		if (empty ($vals['done'])) {
			db_execute (
				'update todo_list set todo = ?, priority = ?, project = ?, person = ? where id = ?',
				$vals['todo'],
				$vals['priority'],
				$vals['project'],
				$vals['person'],
				$vals['id']
			);
		} else {
			db_execute (
				'update todo_list set todo = ?, priority = ?, project = ?, person = ?, done = now() where id = ?',
				$vals['todo'],
				$vals['priority'],
				$vals['project'],
				$vals['person'],
				$vals['id']
			);
		}
		header ('Location: /index/todo-app?pp=' . $vals['pp'] . '&proj=' . $vals['proj']);
		exit;
	}
}

?>
