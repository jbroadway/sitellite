<?php

if ($box['context'] != 'action') {
	echo '<p><a href="' . site_prefix () . '/index/sitestreamer-podcast-action/path.' . rawurlencode ($parameters['path']) . '/title.' . rawurlencode ($parameters['title']) . '">'
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
	'select name, display_title, extension, description, filesize from sitellite_filesystem where path = ? and ' . $acl . ' order by last_modified desc' . $limit,
	$parameters['path']
);

$valid = appconf ('valid');
$mimes = appconf ('mimes');

foreach (array_keys ($res) as $k) {
	if (! in_array (strtolower ($res[$k]->extension), $valid)) {
		unset ($res[$k]);
	}
	if (empty ($res[$k]->display_title)) {
		$res[$k]->display_title = preg_replace ('/[^a-zA-Z0-9-]+/', ' ', $res[$k]->name);
	}
	$res[$k]->type =  $mimes[strtolower ($res[$k]->extension)];
}

header ('Content-Type: application/rss+xml');

template_simple_register ('results', $res);

if (! $parameters['title']) {
	$parameters['title'] = site_domain () . ' Podcast';
}

echo template_simple (
	'podcast.spt',
	array (
		'title' => $parameters['title'],
		'path' => $parameters['path'],
		'desc' => $parameters['descriptions'],
	)
);

exit;

?>