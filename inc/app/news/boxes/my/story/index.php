<?php

if (! isset ($parameters['story'])) {
	header ('Location: ' . site_prefix () . '/index');
	exit;
}

$res = db_single (
	'select * from sitellite_news where id = ?',
	$parameters['story']
);

if (! $res) {
	header ('Location: ' . site_prefix () . '/index');
	exit;
}

$dir = appconf('webpath');
$res->thumb = "http://" . site_domain() . $dir . "/thumbnails/" . $res->thumb;

loader_import ('news.Functions');

echo template_simple (
	'my_story.spt',
	$res
);

?>