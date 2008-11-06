<?php

loader_import ('siteevent.Event');

if (! isset ($parameters['id'])) {
	header ('Location: ' . site_prefix () . '/index/siteevent-app');
	exit;
}

$e = new SiteEvent_Event;

$event = $e->get ($parameters['id']);
$event->_date = $event->date;

$evemt =& siteevent_translate ($event);

if ($event->time == '00:00:00') {
	$event->time = false;
} else {
	$t = $event->time;
	$event->time = intl_time ($event->time);
	if ($event->until_time > $t) {
		$event->time .= ' - ';
		$event->time .= intl_time ($event->until_time);
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

switch ($event->recurring) {
	case 'yearly':
		$event->recur = intl_get ('Yearly');
		break;
	case 'monthly':
		$event->recur = intl_get ('Monthly');
		break;
	case 'weekly':
		$event->recur = intl_date ($event->date, 'l');
		break;
	case 'daily':
	case 'no':
	default:
		if ($event->recurring == 'daily' || $event->until_date != '0000-00-00') {
			$event->recur = intl_get ('Daily');
		} else {
			$event->recur = false;
		}
		break;
}

if ($event->until_date > $event->date) {
	$event->date = intl_date ($event->date, 'shortcevdate') . ' - ' .
		intl_date ($event->until_date, 'cevdate');
} else {
	$event->date = intl_date ($event->date, 'cevdate');
}

if (appconf ('google_maps') && ! empty ($event->loc_address) && empty ($event->loc_map)) {
	$event->loc_google = sprintf (
		'<a href="http://local.google.com/maps?q=%s, %s, %s, %s" target="_blank">%s</a>',
		$event->loc_address,
		$event->loc_city,
		$event->loc_province,
		$event->loc_country,
		intl_get ('Map')
	);
} else {
	$event->loc_google = false;
}

page_title (intl_get (appconf ('siteevent_title')));
echo template_simple (
	'details.spt',
	$event
);

?>
