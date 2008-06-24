<?php

class DeadlinesEditForm extends MailForm {
	function DeadlinesEditForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/deadlines/forms/edit/settings.php');
		page_title ('Deadlines - Edit Item');
		global $cgi;
		$res = db_single ('select * from deadlines where id = ?', $cgi->id);
		foreach ((array) $res as $k => $v) {
			$this->widgets[$k]->setValue ($v);
		}
	}

	function onSubmit ($vals) {
		db_execute (
			'update deadlines
				set title = ?, project = ?, type = ?, ts = ?, details = ?
			where
				id = ?',
			$vals['title'],
			$vals['project'],
			$vals['type'],
			$vals['ts'],
			$vals['details'],
			$vals['id']
		);
		echo '<script language="javascript">
			alert (\'Item Saved.\');
			window.location.href = \'/index/deadlines-app\';
		</script>';
	}
}

?>