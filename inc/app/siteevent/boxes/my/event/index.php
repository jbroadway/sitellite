<?php

if (! isset ($parameters['id'])) {
	header ('Location: ' . site_prefix () . '/index');
	exit;
}

$event = db_single (
	'select * from siteevent_event where id = ?',
	$parameters['id']
);

if (! $event) {
	header ('Location: ' . site_prefix () . '/index');
	exit;
}

if ($event->until_date > $event->date) {
	list ($y, $m, $d) = split ('-', $event->date);
	list ($yy, $mm, $dd) = split ('-', $event->until_date);
	$event->date = strftime ('%B %e', mktime (5, 0, 0, $m, $d, $y)) . ' - ' . strftime ('%B %e, %Y', mktime (5, 0, 0, $mm, $dd, $yy));
} else {
	list ($y, $m, $d) = split ('-', $event->date);
	$event->date = strftime ('%B %e, %Y', mktime (5, 0, 0, $m, $d, $y));
}

if ($event->time == '00:00:00') {
	$event->time = false;
} else {
	list ($h, $m, $s) = split (':', $event->time);
	$t = $event->time;
	$event->time = ltrim (strftime ('%I:%M %p', mktime ($h, $m, $s, $d, $m, $y)), '0');
	if ($event->until_time > $t) {
		$event->time .= ' - ';
		list ($h, $m, $s) = split (':', $event->until_time);
		$event->time .= ltrim (strftime ('%I:%M %p', mktime ($h, $m, $s, $d, $m, $y)), '0');
	}
}

$event->loc_info = false;
if (! $event->loc_info && ! empty ($event->loc_name)) {
	$event->loc_info = true;
}
if (! $event->loc_info && ! empty ($event->loc_address)) {
	$event->loc_info = true;
}
if (! $event->loc_info && ! empty ($event->loc_city)) {
	$event->loc_info = true;
}
if (! $event->loc_info && ! empty ($event->loc_province)) {
	$event->loc_info = true;
}
if (! $event->loc_info && ! empty ($event->loc_country)) {
	$event->loc_info = true;
}
if (! $event->loc_info && ! empty ($event->loc_map)) {
	$event->loc_info = true;
}

$event->contact_info = false;
if (! $event->contact_info && ! empty ($event->contact)) {
	$event->contact_info = true;
}
if (! $event->contact_info && ! empty ($event->contact_email)) {
	$event->contact_info = true;
}
if (! $event->contact_info && ! empty ($event->contact_phone)) {
	$event->contact_info = true;
}
if (! $event->contact_info && ! empty ($event->contact_url)) {
	$event->contact_info = true;
}

$event->_details = trim (strip_tags ($event->details));

if (! empty ($event->_details)) {
	$event->_details = true;
} else {
	$event->_details = false;
}

page_title ($event->title);
echo template_simple (
	'my_event.spt',
	$event
);

?>