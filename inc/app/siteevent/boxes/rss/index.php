<?php

loader_import ('siteevent.Event');

if (! isset ($parameters['limit'])) {
	$parameters['limit'] = 10;
}

$e = new SiteEvent_Event;

$list = $e->getUpcoming ($parameters['limit'], $parameters['category'], $parameters['audience']);

/*
foreach (array_keys ($list) as $k) {
	$item =& $list[$k];
}
*/

header ('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="iso-8859-1"?' . ">\n";
echo template_simple (
	'rss.spt',
	array (
		'list' => $list,
		'rss_title' => appconf ('rss_title'),
		'rss_description' => appconf ('rss_description'),
		'rss_date' => date ('Y-m-d\TH:i:s') . siteevent_timezone (date ('Z')),
		'timezone' => siteevent_timezone (date ('Z')),
	)
);

exit;

?>