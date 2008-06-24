<?php

loader_import ('saf.Database.Generic');

class TimeTrackerEntry extends Generic {
	function TimeTrackerEntry () {
		parent::Generic ('timetracker_entry', 'id');
	}

	function add ($struct) {
		if (isset ($struct['users'])) {
			$users = $struct['users'];
			unset ($struct['users']);
		} else {
			$users = array (session_username ());
		}

		$id = parent::add ($struct);
		if (! $id) {
			return false;
		}
		
		foreach ($users as $user) {
			if (! db_execute ('insert into timetracker_user_entry (id, user_id, entry_id) values (null, ?, ?)', $user, $id)) {
				$this->error = db_error ();
				return false;
			}
		}

		return $id;
	}

	function find ($vals = array ()) {
		$project = $vals['project'];
		$users = $vals['users'];
		$dates = $vals['dates'];

		$sql = '';
		$bind = array ();
		$and = ' ';

		if (count ($users) > 0) {
			$sql .= 'SELECT e.*, u.user_id FROM timetracker_entry e, timetracker_user_entry u WHERE e.id = u.entry_id and u.user_id in(';
			$sep = '';
			foreach ($users as $user) {
				$sql .= $sep . '?';
				$bind[] = $user;
				$sep = ', ';
			}
			$sql .= ')' . NEWLINE;
			$and = ' AND ';
		} else {
			$sql .= 'SELECT * FROM timetracker_entry e WHERE' . NEWLINE;
		}

		if (! empty ($project)) {
			$sql .= $and . 'e.project_id = ?';
			$bind[] = $project;
			$and = ' AND ';
		}

		if (is_array ($dates)) {
			// range
			$sql .= $and . 'e.started >= ? AND e.started <= ?' . NEWLINE;
			$bind[] = array_shift ($dates);
			$bind[] = array_shift ($dates);
		} elseif (isset ($vals['dates'])) {
			// single date
			if (! is_numeric ($dates)) {
				$dates = strtotime ($dates);
			}

			$sql .= $and . 'YEAR(e.started) = ? AND MONTH(e.started) AND DAY(e.started) = ?';
			$bind[] = date ('Y', $dates);
			$bind[] = date ('m', $dates);
			$bind[] = date ('d', $dates);
		}

		return $this->query ($sql, $bind);
	}
}

?>