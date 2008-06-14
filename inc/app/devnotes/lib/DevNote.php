<?php

loader_import ('saf.Database.Generic');

class DevNote extends Generic {
	function DevNote () {
		parent::Generic ('devnotes_message', 'id');
	}

	function getApps () {
		$res = db_fetch ('select appname, count(*) as note_count from devnotes_message group by appname');
		if (! $res) {
			$this->error = db_error ();
			$res = array ();
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		return $res;
	}

	function userMessages ($user) {
		$res = db_fetch ('select * from devnotes_message where name = ? order by ts desc', $user);
		if (! $res) {
			$this->error = db_error ();
			return array ();
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		return $res;
	}
}

?>