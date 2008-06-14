<?php

/**
 * @package CMS
 */
class WorkspaceBookmark {
	var $error;

	function getList () {
		$res = db_fetch ('select * from sitellite_bookmark where user = ? order by name asc', session_username ());
		if (! $res) {
			$this->error = db_error ();
			return false;
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		return $res;
	}

	function add ($link, $name) {
		$res = db_execute ('insert into sitellite_bookmark (id, user, link, name) values (null, ?, ?, ?)', session_username (), $link, $name);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	function delete ($id) {
		$res = db_execute ('delete from sitellite_bookmark where user = ? and id = ?', session_username (), $id);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}
}

?>