<?php

if (! session_valid ()) {
	page_title (intl_get ('You must be logged in to subscribe'));
	echo template_simple ('subscribe_not_registered.spt', $parameters);
	return;
}

db_execute (
	'insert into siteforum_subscribe (id, post_id, user_id) values (null, ?, ?)',
	$parameters['post'],
	session_username ()
);

header ('Location: ' . site_prefix () . '/index/siteforum-list-action/post.' . $parameters['post']);
exit;

?>