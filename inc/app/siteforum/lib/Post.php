<?php

loader_import ('saf.Database.Generic');

class SiteForum_Post extends Generic {
	function SiteForum_Post () {
		parent::Generic ('siteforum_post', 'id', 'topic_id');
		$this->usePermissions = true;
	}

	function getThreads ($topic) {
		if (session_admin ()) {
			$perms = session_allowed_sql ();
		} else {
			$perms = session_approved_sql ();
		}

		$q = db_query ('SELECT * FROM siteforum_post WHERE topic_id = ? AND post_id = "" AND ' . $perms . ' ORDER BY notice DESC, mtime DESC, ts DESC');
		if ($q->execute ($topic)) {
			$this->total = $q->rows ();
			$res = $q->fetch ($this->offset, $this->limit);
			$q->free ();
		} else {
			$this->error = $q->error ();
			return array ();
		}

		foreach (array_keys ($res) as $k) {
			$res[$k]->public = db_shift ('select public from sitellite_user where username = ?', $res[$k]->user_id);
			$res[$k]->count = db_shift ('SELECT count(*) FROM siteforum_post where post_id = ?', $res[$k]->id);
			$obj = db_single ('SELECT ts, user_id, id FROM siteforum_post where id = ? or post_id = ? order by ts desc limit 1', $res[$k]->id, $res[$k]->id);
			$res[$k]->last_post = $obj->ts;
            $res[$k]->last_post_user = $obj->user_id;
            $res[$k]->last_post_id = $obj->id;
            $res[$k]->last_post_user_public = db_shift ('select public from sitellite_user where username = ?', $obj->user_id);
		}

		return $res;
	}

	function find ($fid) {
		$res = parent::find($fid);
		
		foreach (array_keys ($res) as $k) {
			$res[$k]->public = db_shift ('select public from sitellite_user where username = ?', $res[$k]->user_id);
			$res[$k]->count = db_shift ('SELECT count(*) FROM siteforum_post where post_id = ?', $res[$k]->id);
			$obj = db_single ('SELECT ts, user_id, id FROM siteforum_post where id = ? or post_id = ? order by ts desc limit 1', $res[$k]->id, $res[$k]->id);
			$res[$k]->last_post = $obj->ts;
            $res[$k]->last_post_user = $obj->user_id;
            $res[$k]->last_post_id = $obj->id;
            $res[$k]->last_post_user_public = db_shift ('select public from sitellite_user where username = ?', $obj->user_id);
		}

		return $res;
	}

	function getThread ($post, $reverse = false) {
		if (session_admin ()) {
			$perms = session_allowed_sql ();
		} else {
			$perms = session_approved_sql ();
		}

		if ($reverse) {
			$append = ' ORDER BY ts DESC';
		} else {
			$append = ' ORDER BY ts ASC';
		}

		$q = db_query ('SELECT * FROM siteforum_post WHERE (id = ? OR post_id = ?) AND ' . $perms . $append);
		if ($q->execute ($post, $post)) {
			$this->total = $q->rows ();
			$res = $q->fetch ($this->offset, $this->limit);
			$q->free ();
		} else {
			$this->error = $q->error ();
			return array ();
		}

		foreach (array_keys ($res) as $k) {
			$res[$k]->posts = db_shift ('select count(*) from siteforum_post where user_id = ?', $res[$k]->user_id);
			$user = db_single ('select public, province, country from sitellite_user where username = ?', $res[$k]->user_id);
			$res[$k]->public = $user->public;
			if ($user->public == 'yes') {
				$res[$k]->location = '';
				$concat = '';
				if (! empty ($user->province)) {
					$res[$k]->location .= $concat . $user->province;
					$concat = ', ';
				}
				if (! empty ($user->country)) {
					$res[$k]->location .= $concat . $user->country;
				}
			}
		}

		return $res;
	}

	function getLatest ($limit = 5, $topic = false) {
		if (session_admin ()) {
			$perms = session_allowed_sql ();
		} else {
			$perms = session_approved_sql ();
		}

		if ($topic) {
			$list = db_fetch_array (
				'select id, topic_id, user_id, ts, subject from siteforum_post where topic_id = ? and ' . $perms . ' order by ts desc limit ' . $limit,
				$topic
			);
		} else {
			$list = db_fetch_array (
				'select id, topic_id, user_id, ts, subject from siteforum_post where ' . $perms . ' order by ts desc limit ' . $limit
			);
		}
		if (! $list) {
			return array ();
		}

		loader_import ('siteforum.Topic');

		$t = new SiteForum_Topic;

		foreach (array_keys ($list) as $k) {
			$list[$k]->topic_name = $t->getTitle ($list[$k]->topic_id);
			$list[$k]->user_public = db_shift ('select public from sitellite_user where username = ?', $list[$k]->user_id);
		}

		return $list;
	}

	function touch ($id) {
		return db_execute ('update siteforum_post set mtime = now() where id = ?', $id);
	}

	/**
	 * Can they access a post?
	 */
	function allowed ($id) {
		if (session_admin ()) {
			$perms = session_allowed_sql ();
		} else {
			$perms = session_approved_sql ();
		}
		return db_shift (
			'select count(*) from siteforum_post where id = ? and ' . $perms,
			$id
		);
	}

	/**
	 * Get an individual post's attachment.
	 */
	function getAttachment ($id) {
		return db_single (
			'select * from siteforum_attachment where post_id = ?',
			$id
		);
	}

	/**
	 * If a thread has attachments.
	 */
	function hasAttachments ($id) {
		return db_shift (
			'select count(*) from siteforum_attachment where post_id = ? or parent_post = ?',
			$id,
			$id
		);
	}
}

?>
