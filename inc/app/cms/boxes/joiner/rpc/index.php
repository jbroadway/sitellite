<?php

loader_import ('saf.Misc.RPC');

class Joiner_RPC {
	function add ($name, $primary_id, $foreign_id, $table, $field1, $field2) {
		if (! $primary_id || $primary_id == 'false') {
			$list = session_get ($name . '_joiner');
			if (! is_array ($list)) {
				$list = array ();
			}
			if (! in_array ($foreign_id, $list)) {
				$list[] = $foreign_id;
			}
			session_set ($name . '_joiner', $list);
			return true;
		}
		return db_execute (
			sprintf (
				'insert into %s (%s, %s) values (?, ?)',
				$table,
				$field1,
				$field2
			),
			$primary_id,
			$foreign_id
		);
	}

	function remove ($name, $primary_id, $foreign_id, $table, $field1, $field2) {
		if (! $primary_id || $primary_id == 'false') {
			$list = session_get ($name . '_joiner');
			if (! is_array ($list)) {
				$list = array ();
			}
			foreach ($list as $k => $v) {
				if ($v == $foreign_id) {
					unset ($list[$k]);
				}
			}
			session_set ($name . '_joiner', $list);
			return true;
		}
		return db_execute (
			sprintf (
				'delete from %s where %s = ? and %s = ?',
				$table,
				$field1,
				$field2
			),
			$primary_id,
			$foreign_id
		);
	}
}

echo rpc_handle (new Joiner_RPC (), $parameters);
exit;

?>