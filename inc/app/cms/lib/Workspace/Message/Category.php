<?php

/**
 * @package CMS
 * @category Workspace
 */

class WorkspaceMessageCategory {
	var $error;

	function getList ($user = false) {
		if (! $user) {
			$user = session_username ();
		}
		$res = db_fetch ('select * from sitellite_msg_category where user = ? order by name asc', $user);
		if (! $res) {
			$this->error = db_error ();
			$res = array ();
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		foreach (array_keys ($res) as $k) {
			$res[$k]->count = db_shift ('select count(*) from sitellite_msg_recipient where status != "trash" and type = "user" and user = ? and category = ?', $user, $res[$k]->name);
		}
		return $res;
	}

	function add ($name, $user = false) {
		if (! $user) {
			$user = session_username ();
		}
		$res = db_execute ('insert into sitellite_msg_category (name, user) values (?, ?)', $name, $user);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	function rename ($new, $name, $user = false) {
		if (! $user) {
			$user = session_username ();
		}
		$res = db_execute ('update sitellite_msg_category set name = ? where name = ? and user = ?', $new, $name, $user);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}

	function delete ($name, $user = false) {
		if (! $user) {
			$user = session_username ();
		}
		$res = db_execute ('delete from sitellite_msg_category where name = ? and user = ?', $name, $user);
		if (! $res) {
			$this->error = db_error ();
		}
		return $res;
	}
}

?>