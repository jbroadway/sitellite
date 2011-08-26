<?php

if (empty ($parameters['poll']) || empty ($parameters['option'])) {
	header ('Location: ' . site_prefix () . '/index/sitepoll-app');
	exit;
}

$voted = db_shift (
	'select count(*) from sitepoll_vote where poll = ? and ua = ? and ip = ?',
	$parameters['poll'],
	$_SERVER['HTTP_USER_AGENT'],
	$_SERVER['REMOTE_ADDR']
);

if (! $voted) {
	db_execute (
		'insert into sitepoll_vote
			(id, poll, choice, ts, ua, ip)
		values
			(null, ?, ?, now(), ?, ?)',
		$parameters['poll'],
		$parameters['option'],
		$_SERVER['HTTP_USER_AGENT'],
		$_SERVER['REMOTE_ADDR']
	);
}

header ('Location: ' . site_prefix () . '/index/sitepoll-results-action/poll.' . $parameters['poll']);
exit;

?>