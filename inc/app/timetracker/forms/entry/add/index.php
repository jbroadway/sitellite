<?php

class EntryAddForm extends MailForm {
	function EntryAddForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/timetracker/forms/entry/add/settings.php');

		$res = db_fetch ('select username, firstname, lastname from sitellite_user order by lastname asc');
		if (! $res) {
			$res = array ();
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		$users = array ();
		foreach ($res as $row) {
			if (! empty ($row->lastname)) {
				$users[$row->username] = $row->lastname;
				if (! empty ($row->firstname)) {
					$users[$row->username] .= ', ' . $row->firstname;
				}
				$users[$row->username] .= ' (' . $row->username . ')';
			} else {
				$users[$row->username] = $row->username;
			}
		}
		$this->widgets['users']->setValues ($users);
		$this->widgets['users']->setDefault (session_username ());
		$this->widgets['users']->addRule ('not empty', 'You must select at least one user.');

		$this->widgets['started']->setDefault (date ('Y-m-d H:i:s'));
		$this->widgets['ended']->setDefault (date ('Y-m-d H:i:s'));

		global $cgi;
		$this->widgets['proj_name']->setValue (db_shift ('select name from timetracker_project where id = ?', $cgi->project));

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="history.go (-1); return false"';
	}

	function onSubmit ($vals) {
		//echo '<pre>';
		//print_r ($vals);
		//exit;
		$duration = (((strtotime ($vals['ended']) - strtotime ($vals['started'])) / 60) / 60);

		$res = db_execute ('insert into timetracker_entry
				(id, project_id, task_description, started, duration)
			values
				(null, ?, ?, ?, ?)',
			$vals['project'],
			$vals['description'],
			$vals['started'],
			$duration
		);
		if (! $res) {
			return '<p>Unknown error: ' . db_error () . '</p>';
		}

		$eid = db_lastid ();

		if (! is_array ($vals['users'])) {
			$vals['users'] = preg_split ('/, ?/', $vals['users']);
		}
		foreach ($vals['users'] as $user) {
			db_execute ('insert into timetracker_user_entry
					(id, user_id, entry_id)
				values
					(null, ?, ?)',
				$user,
				$eid
			);
		}

		header ('Location: ' . site_prefix () . '/index/timetracker-app/added.entry');
		exit;
	}
}

page_title (intl_get ('TimeTracker') . ' - ' . intl_get ('Add Entry'));
$form = new EntryAddForm;
echo $form->run ();

?>