<?php

if (! isset ($parameters['status'])) {
	$parameters['status'] = 'approved';
	$status = 'approved';
} elseif ($parameters['status'] = 'pending') {
	$status = 'draft';
} else {
	$status = $parameters['status'];
}

if ($parameters['status'] == 'approved') {
	$url = site_prefix () . '/index/siteevent-details-action/id.';
} else {
	$url = site_prefix () . '/index/siteevent-my-event-action/id.';
}

if (! isset ($parameters['user'])) {
	$parameters['user'] = session_username ();
}

$res = db_fetch_array (
	'select id, title, date, category from siteevent_event where sitellite_owner = ? and sitellite_status = ? order by date desc, time desc',
	$parameters['user'],
	$status
);

if ($box['context'] == 'action') {
	page_title (ucfirst ($parameters['status']) . ' ' . intl_get ('Event Listings') . ' (' . count ($res) . ')');
}

loader_import ('siteevent.Event');

foreach (array_keys ($res) as $k) {
	list ($y, $m, $d) = split ('-', $res[$k]->date);
	$res[$k]->date = strftime ('%B %e, %Y', mktime (5, 0, 0, $m, $d, $y));
}

echo template_simple (
	'my_submissions.spt',
	array (
		'list' => $res,
		'url' => $url,
	)
);

?>