<?php

$url = site_prefix () . '/index/siteevent-details-action/id.';

if (! isset ($parameters['user'])) {
	$parameters['user'] = session_username ();
}

$res = db_fetch_array (
	'select id, title, date, category from siteevent_event where sitellite_owner = ? and sitellite_status = ? order by date desc, time desc',
	$parameters['user'],
	'approved'
);

if ($box['context'] == 'action') {
	page_title (intl_get ('Event Listings') . ' / ' . intl_get ('By') . $parameters['user']);
}

loader_import ('siteevent.Event');

foreach (array_keys ($res) as $k) {
	list ($y, $m, $d) = split ('-', $res[$k]->date);
	$res[$k]->date = strftime ('%B %e, %Y', mktime (5, 0, 0, $m, $d, $y));
}

echo template_simple (
	'my_events.spt',
	array (
		'list' => $res,
		'url' => $url,
	)
);

?>