<?php

$sql = 'select * from sitequotes_entry order by rand()';

if ($parameters['limit']) {
	$sql .= ' limit ' . $parameters['limit'];
}

if ($context == 'action') {
	page_title (appconf ('title'));
}

echo template_simple (
	'list.spt',
	array (
		'list' => db_fetch_array ($sql),
	)
);

?>