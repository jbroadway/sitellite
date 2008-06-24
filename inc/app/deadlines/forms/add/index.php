<?php

class DeadlinesAddForm extends MailForm {
	function DeadlinesAddForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/deadlines/forms/add/settings.php');
		page_title ('Deadlines - Add Item');
		$this->widgets['ts']->setDefault (date ('Y-m-d H:i:s'));
	}

	function onSubmit ($vals) {
		db_execute (
			'insert into deadlines
				(id, title, project, type, ts, details)
			values
				(null, ?, ?, ?, ?, ?)',
			$vals['title'],
			$vals['project'],
			$vals['type'],
			$vals['ts'],
			$vals['details']
		);
		echo '<script language="javascript">
			alert (\'Item Added.\');
			window.location.href = \'/index/deadlines-app\';
		</script>';
	}
}

?>