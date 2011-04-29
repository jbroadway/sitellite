<?php

loader_import ('siteevent.Event');

if (! isset ($parameters['day'])) {
	header ('Location: ' . site_prefix () . '/index/siteevent-app');
	exit;
}

header ('Location: ' . site_prefix () . '/index/siteevent-app/view.day/day.' . $parameters['day']);
exit;

$e = new SiteEvent_Event;

$events = $e->getDay ($parameters['day']);

list ($y, $mm, $d) = split ('-', $parameters['day']);

foreach (array_keys ($events) as $k) {
	$event =& $events[$k];

	if ($event->time == '00:00:00') {
		$event->time = '';
	} else {
		list ($h, $m, $s) = split (':', $event->time);
		$t = $event->time;
		$event->time = ltrim (strftime ('%I:%M %p', mktime ($h, $m, $s, $d, $mm, $y)), '0');
		if ($event->until_time > $t) {
			$event->time .= ' - ';
			list ($h, $m, $s) = split (':', $event->until_time);
			$event->time .= ltrim (strftime ('%I:%M %p', mktime ($h, $m, $s, $d, $mm, $y)), '0');
		}
		$event->time .= ': ';
	}
}

list ($y, $m, $d) = split ('-', $parameters['day']);
page_title (intl_get ('Events For') . ' ' . strftime ('%B %e, %Y', mktime (5, 0, 0, $m, $d, $y)));
echo template_simple (
	'day.spt',
	array (
		'list' => $events,
	)
);

?>