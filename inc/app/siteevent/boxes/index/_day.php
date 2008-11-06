<?php

if (! isset ($parameters['day'])) {
	$parameters['day'] = date ('Y-m-d');
}

$events = $e->getDay ($parameters['day']);

$events =& siteevent_translate ($events);

list ($y, $mm, $d) = split ('-', $parameters['day']);

foreach (array_keys ($events) as $k) {
	$event =& $events[$k];

	if (! empty ($parameters['category'])) {
		if ($event->category != $parameters['category']) {
			unset ($events[$k]);
			continue;
		}
	}

	if (! empty ($parameters['audience'])) {
		if ($event->audience != $parameters['audience']) {
			unset ($events[$k]);
			continue;
		}
	}

	if ($event->time == '00:00:00') {
		$event->time = '';
	} else {
		$t = $event->time;
		$event->time = intl_time ($event->time);
		if ($event->until_time > $t) {
			$event->time .= ' - ';
			$event->time .= intl_time ($event->until_time);
		}
	}
}

//page_title (intl_get ('Events For') . ' ' . intl_date ($parameters['day']));
page_title (intl_get (appconf ('siteevent_title')));
echo template_simple (
	'categories.spt',
	array (
		'list' => db_fetch_array ('select * from siteevent_category order by name asc'),
		'current' => $parameters['category'],
		'alist' => db_fetch_array ('select * from siteevent_audience order by name asc'),
		'audience' => $parameters['audience'],
		'simplecal' => $parameters['simplecal'],
		'view' => $parameters['view'],
	)
);
echo template_simple (
	'day.spt',
	array (
		'list' => $events,
		'date' => intl_date ($parameters['day'], 'cevdate'),
		'prevDate' => Date::subtract ($parameters['day'], '1 day'),
		'prevDay' => intl_date (Date::subtract ($parameters['day'], '1 day'), 'cevdate'),
		'nextDate' => Date::add ($parameters['day'], '1 day'),
		'nextDay' => intl_date (Date::add ($parameters['day'], '1 day'), 'cevdate'),
	)
);

echo '<p>';
if (! empty ($parameters['category'])) {
    $cat = '?category=' . $parameters['category'];
} else {
    $cat = '';
}
if (! empty ($parameters['audience'])) {
    $aud = (empty ($cat)?'?':'&') .
        'audience=' . $parameters['audience'];
} else {
    $aud = '';
}
if (appconf ('ical_links')) {
    echo '<a href="' . site_prefix () . '/index/siteevent-ical-action' .                $cat . $aud . '">' . intl_get ('Subscribe (iCalendar)') . '</a> &nbsp; &nbsp; &nbsp; &nbsp;';
}
if (appconf ('rss_links')) {
    echo '<a href="' . site_prefix () . '/index/siteevent-rss-action' .                 $cat . $aud . '">' . intl_get ('Subscribe (RSS)') . '</a> &nbsp; &nbsp; &nbsp; &nbsp;';
}
echo '</p>';

page_title (appconf ('siteevent_title'));
if (appconf ('template_calendar')) {
	page_template (appconf ('template_calendar'));
} elseif (appconf ('template')) {
	page_template (appconf ('template'));
}

?>
