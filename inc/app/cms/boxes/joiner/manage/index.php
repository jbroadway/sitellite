<?php

if (! preg_match ('/^[a-zA-Z0-9_]+$/', $parameters['table'])) {
	die ('Invalid table');
}

if (! preg_match ('/^[a-zA-Z0-9_]+$/', $parameters['key'])) {
	die ('Invalid key field');
}

if (! preg_match ('/^[a-zA-Z0-9_]+$/', $parameters['title'])) {
	die ('Invalid title field');
}

if ($parameters['add']) {
	db_execute (
		sprintf (
			'insert into %s (%s, %s) values (null, ?)',
			$parameters['table'],
			$parameters['key'],
			$parameters['title']
		),
		$parameters['add']
	);
}

if ($parameters['del']) {
	db_execute (
		sprintf (
			'delete from %s where %s = ?',
			$parameters['table'],
			$parameters['key']
		),
		$parameters['del']
	);
}

page_title (intl_get ('Add/Remove Items'));

$parameters['list'] = db_pairs ('select * from ' . $parameters['table'] . ' order by ' . $parameters['title'] . ' asc');

$parameters['height'] = count ($parameters['list']) * 24;
if ($parameters['height'] > 400) {
	$parameters['height'] = 400;
}

echo template_simple ('joiner.spt', $parameters);

?>