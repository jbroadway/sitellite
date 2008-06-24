<?php

loader_import ('saf.Database.Generic');

class SiteForum_Topic extends Generic {
	function SiteForum_Topic () {
		parent::Generic ('siteforum_topic', 'id');
		$this->usePermissions = true;
	}

	function getTopics () {
		if (session_admin ()) {
			$perms = session_allowed_sql ();
		} else {
			$perms = session_approved_sql ();
		}

        $list = db_fetch_array ('select * from siteforum_topic where ' . $perms . ' order by
name asc');

        foreach (array_keys ($list) as $k) {
            $list[$k]->threads = db_shift ('select count(*) from siteforum_post where topic_id = ? and post_id = ""', $list[$k]->id);
            $list[$k]->posts = db_shift ('select count(*) from siteforum_post where topic_id = ?', $list[$k]->id);
            $obj = db_single ('select ts, user_id, id from siteforum_post where topic_id = ? order by ts desc limit 1', $list[$k]->id);
            $list[$k]->last_post = $obj->ts;
            $list[$k]->last_post_user = $obj->user_id;
            $list[$k]->last_post_id = $obj->id;
            $list[$k]->last_post_user_public = db_shift ('select public from sitellite_user where username = ?', $obj->user_id);
        }
                                                                                
        return $list;
    }

	function getTitle ($id) {
		return db_shift (
			'select name from siteforum_topic where id = ?',
			$id
		);
	}
}

?>
