<?php

if ($box['context'] != 'action') {
	echo '<p><a href="' . site_prefix () . '/index/sitestreamer-playlist-action?path=' . $parameters['path'] . '">'
		. intl_get ($parameters['title'])
		. '</a></p>';

	return;
}

if (session_admin ()) {
	$acl = session_allowed_sql ();
} else {
	$acl = session_approved_sql ();
}

if (! $parameters['limit'] || $parameters['limit'] == 0 || ! is_numeric ($parameters['limit'])) {
	$limit = '';
} else {
	$limit = ' limit ' . $parameters['limit'];
}

$res = db_fetch_array (
	'select name, display_title, extension from sitellite_filesystem where path = ? and ' . $acl . ' order by last_modified desc' . $limit,
	$parameters['path']
);

header ('Content-Type: audio/x-mpegurl');

echo "#EXTM3U\r\n";

$valid = appconf ('valid');

foreach (array_keys ($res) as $k) {
	if (! in_array (strtolower ($res[$k]->extension), $valid)) {
		continue;
	}
	if (empty ($res[$k]->display_title)) {
		$res[$k]->display_title = preg_replace ('/[^a-zA-Z0-9-]+/', ' ', $res[$k]->name);
	}
	echo '#EXTINF:' . $res[$k]->display_title . "\r\n";
	echo site_url () . '/index/cms-filesystem-action?file=' . $parameters['path'] . '/' . $res[$k]->name . '.' . $res[$k]->extension . "\r\n";
}

exit;

?>