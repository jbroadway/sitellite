<?php

loader_import ('siteevent.Event');
loader_import ('saf.Date');
loader_import ('saf.Date.vCalendar');

if (! isset ($parameters['limit'])) {
	$parameters['limit'] = 50;
}

$e = new SiteEvent_Event;

$list = $e->getUpcoming ($parameters['limit'], $parameters['category'], $parameters['audience']);

$cal = new vCal ();
$cal->addProperty ('METHOD', 'PUBLISH');
$cal->addProperty ('CALSCALE', 'GREGORIAN');
$cal->addProperty ('PRODID', '-//Sitellite CMS//NONSGML SiteEvent//EN');
$cal->addProperty ('VERSION', '2.0');

foreach (array_keys ($list) as $k) {
	$item =& $list[$k];

	$e =& $cal->addEvent ('VEVENT');

	$e->addProperty ('UID', site_domain () . '/siteevent/' . $item->id);

	$e->addProperty ('SEQUENCE', $k + 1);

	$p =& $e->addProperty ('URL', 'http://' . site_domain () . site_prefix () . '/index/siteevent-details-action/id.' . $item->id . '/title.' . siteevent_filter_link_title ($item->title));
	$p->addParameter ('VALUE', 'URI');

	$e->addProperty ('STATUS', 'CONFIRMED');

	if ($item->time && $item->time > '00:00:00') {
		$e->addProperty ('DTSTART', Date::timestamp ($item->date . ' ' . $item->time, 'Ymd\THis'));
	} else {
		$p =& $e->addProperty ('DTSTART', Date::format ($item->date, 'Ymd'));
		$p->addParameter ('VALUE', 'DATE');
	}

	if ($item->until_date && $item->until_date > '0000-00-00') {
		if ($item->until_time && $item->until_time > '00:00:00') {
			$e->addProperty ('DTEND', Date::timestamp ($item->until_date . ' ' . $item->until_time, 'Ymd\THis'));
		} else {
			$p =& $e->addProperty ('DTEND', Date::format ($item->until_date, 'Ymd'));
			$p->addParameter ('VALUE', 'DATE');
		}
	}

	$e->addProperty ('CATEGORIES', strtoupper ($item->category));

	$e->addProperty ('SUMMARY', $item->title);

	$details = strip_tags ($item->details);
	$details = preg_replace ('|[\r\n]|s', '\\n', $details);
	$e->addProperty ('DESCRIPTION', $details);

	if (! empty ($item->contact_email)) {
		$p =& $e->addProperty ('ORGANIZER', array ('MAILTO', $item->contact_email));
		if (! empty ($item->contact)) {
			$p->addParameter ('CN', $item->contact);
		}
	}

	$loc = '';
	$concat = '';
	foreach (get_object_vars ($item) as $k => $v) {
		if ($k == 'loc_map') {
			continue;
		}
		if (strpos ($k, 'loc_') === 0 && ! empty ($v)) {
			$loc .= $concat . $v;
			$concat = ', ';
		}
	}
	if (! empty ($loc)) {
		$e->addProperty ('LOCATION', $loc);
	}
}

header ('Content-Type: text/calendar');
echo $cal->write ();
exit;

?>