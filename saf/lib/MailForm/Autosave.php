<?php

class Autosave {
	function update () {
		// post rpc requests should use $_POST
		if (! is_array ($vals)) {
			$vals = $_POST;
		}

		$url = $vals['autosave_url'];
		unset ($vals['autosave_url']);
		$url = str_replace ('#', '', $url);
		$md5 = md5 ($url);

		$title = $vals['autosave_title'];
		unset ($vals['autosave_title']);

		unset ($vals['_key']);
		unset ($vals['_return']);
		unset ($vals['_collection']);

		$vals = serialize ($vals);

		if (db_shift ('select count(*) from sitellite_autosave where user_id = ? and md5 = ?', session_username (), $md5)) {
			db_execute (
				'update sitellite_autosave set vals = ?, page_title = ?, ts = now() where user_id = ? and md5 = ?',
				$vals,
				$title,
				session_username (),
				$md5
			);
		} else {
			db_execute (
				'insert into sitellite_autosave values (?, ?, ?, ?, now(), ?)',
				session_username (),
				$md5,
				$url,
				$title,
				$vals
			);
		}
		return true;
	}

	function retrieve ($url = false) {
		if (! $url) {
			$url = site_url () . $_SERVER['REQUEST_URI'];
			if (conf ('Site', 'remove_index')) {
				$url = str_replace ('/index/', '/', $url);
			}
		}
		$md5 = md5 ($url);
		return unserialize (
			db_shift (
				'select vals from sitellite_autosave where user_id = ? and md5 = ?',
				session_username (),
				$md5
			)
		);
	}

	function retrieve_all () {
		return db_fetch_array ('select url, page_title, ts from sitellite_autosave where user_id = ?', session_username ());
	}

	function count_all () {
		return db_shift ('select count(*) from sitellite_autosave where user_id = ?', session_username ());
	}

	function clear ($url = false) {
		if (! $url) {
			$url = site_url () . $_SERVER['REQUEST_URI'];
			if (conf ('Site', 'remove_index')) {
				$url = str_replace ('/index/', '/', $url);
			}
		}
		$md5 = md5 ($url);
		return db_execute (
			'delete from sitellite_autosave where user_id = ? and md5 = ?',
			session_username (),
			$md5
		);
	}

	function clear_all ($url = false) {
		return db_execute (
			'delete from sitellite_autosave where user_id = ?',
			session_username ()
		);
	}

	function has_draft () {
		$url = site_url () . $_SERVER['REQUEST_URI'];
		if (conf ('Site', 'remove_index')) {
			$url = str_replace ('/index/', '/', $url);
		}
		if ($this->retrieve ($url)) {
			return true;
		}
		return false;
	}
}

?>