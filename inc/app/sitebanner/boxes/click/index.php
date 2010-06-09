<?php

if (empty ($parameters['id'])) {
	header ('Location: ' . site_prefix () . '/');
	exit;
}

$banner = db_single (
	'select url, client from sitebanner_ad where id = ?',
	$parameters['id']
);

if ($banner->client != session_username ()) {
	db_execute (
		'insert into sitebanner_click
			(id, campaign, ip, ts, ua)
		values
			(null, ?, ?, now(), ?)',
		$parameters['id'],
		$_SERVER['REMOTE_ADDR'],
		$_SERVER['HTTP_USER_AGENT']
	);
}

header ('Location: ' . $banner->url);
exit;

?>