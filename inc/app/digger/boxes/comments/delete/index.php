<?php

if (! session_admin() || ! isset($parameters['id'])) {
    header('Location: ' . site_prefix() . '/index/digger-app');
    exit;
}

db_execute(
	'delete from digger_comments where id = ?',
	$parameters['id']
);

header('Location: ' . site_prefix() . '/index/digger-comments-action/id.' . $parameters['story']);
exit;

?>