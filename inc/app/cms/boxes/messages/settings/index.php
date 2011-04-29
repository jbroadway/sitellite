<?php

global $cgi, $conf;

if (! isset ($cgi->submit_button)) {
	$res = db_fetch_array ('select * from sitellite_msg_forward where user = ?', session_username ());

	$data = array ('jabber_setup' => $conf['Messaging']['jabber']);

	foreach (array_keys ($res) as $k) {
		$data[$res[$k]->priority . '_location'] = $res[$k]->location;
		$data[$res[$k]->priority . '_info'] = $res[$k]->info;
	}
	echo template_simple ('messages/settings.spt', $data);
	return;

} else {
	$res = db_fetch_array ('select * from sitellite_msg_forward where user = ?', session_username ());

	$data = array ();
	foreach (array_keys ($res) as $k) {
		$data[$res[$k]->priority . '_location'] = $res[$k]->location;
		$data[$res[$k]->priority . '_info'] = $res[$k]->info;
	}

	if (! empty ($cgi->all_info)) {
		if (isset ($data['all_info'])) {
			// update
			db_execute (
				'update sitellite_msg_forward set location = ?, info = ? where user = ? and priority = ?',
				$cgi->all_location,
				$cgi->all_info,
				session_username (),
				'all'
			);
		} else {
			// insert
			db_execute (
				'insert into sitellite_msg_forward
					(id, user, location, info, priority)
				values
					(null, ?, ?, ?, ?)',
				session_username (),
				$cgi->all_location,
				$cgi->all_info,
				'all'
			);
		}
	} elseif (isset ($data['all_info'])) {
		// delete
		db_execute (
			'delete from sitellite_msg_forward where user = ? and priority = ?',
			session_username (),
			'all'
		);
	}

	/*if (! empty ($cgi->normal_info)) {
		if (isset ($data['normal'])) {
			// update
		} else {
			// insert
		}
	} elseif (isset ($data['normal'])) {
		// delete
	}*/

	if (! empty ($cgi->high_info)) {
		if (isset ($data['high_info'])) {
			// update
			db_execute (
				'update sitellite_msg_forward set location = ?, info = ? where user = ? and priority = ?',
				$cgi->high_location,
				$cgi->high_info,
				session_username (),
				'high'
			);
		} else {
			// insert
			db_execute (
				'insert into sitellite_msg_forward
					(id, user, location, info, priority)
				values
					(null, ?, ?, ?, ?)',
				session_username (),
				$cgi->high_location,
				$cgi->high_info,
				'high'
			);
		}
	} elseif (isset ($data['high_info'])) {
		// delete
		db_execute (
			'delete from sitellite_msg_forward where user = ? and priority = ?',
			session_username (),
			'high'
		);
	}

	if (! empty ($cgi->urgent_info)) {
		if (isset ($data['urgent_info'])) {
			// update
			db_execute (
				'update sitellite_msg_forward set location = ?, info = ? where user = ? and priority = ?',
				$cgi->urgent_location,
				$cgi->urgent_info,
				session_username (),
				'urgent'
			);
		} else {
			// insert
			db_execute (
				'insert into sitellite_msg_forward
					(id, user, location, info, priority)
				values
					(null, ?, ?, ?, ?)',
				session_username (),
				$cgi->urgent_location,
				$cgi->urgent_info,
				'urgent'
			);
		}
	} elseif (isset ($data['urgent_info'])) {
		// delete
		db_execute (
			'delete from sitellite_msg_forward where user = ? and priority = ?',
			session_username (),
			'urgent'
		);
	}
}

// respond
//page_title (intl_get ('Preferences Saved!'));
//echo '<p><a href="' . site_prefix () . '/index/cms-messages-action">' . intl_get ('Back') . '</a></p>';
header ('Location: ' . site_prefix () . '/index/cms-messages-action?_msg=' . urlencode ('Your preferences have been saved!'));
exit;

?>