<?php

/*function dump_items ($items) {
	echo '<p><table border="1" cellpadding="3" cellspacing="1">';
	foreach ($items as $k => $v) {
		echo sprintf (
			'<tr><td>%d</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
			$v->id,
			$v->date,
			$v->until_date,
			$v->recurring,
			$v->title
		);
	}
	echo '</table></p>';
}*/

$parameters['limit'] = 20;

$list = $e->getUpcoming ($parameters['limit'], $parameters['category'], $parameters['audience']);

page_title (appconf ('siteevent_title'));
if (appconf ('template_calendar')) {
	page_template (appconf ('template_calendar'));
} elseif (appconf ('template')) {
	page_template (appconf ('template'));
}

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

$dates = array ();
foreach ($list as $k => $event) {
	$day = $event->date;
	$dd = strftime ('%A, %B %e', strtotime ($event->date));

	if ($event->time == '00:00:00') {
		$event->time = false;
	} else {
		list ($h, $m, $s) = split (':', $event->time);
		$t = $event->time;
		$event->ftime = ltrim (strftime ('%I:%M %p', mktime ($h, $m, $s, $d, $m, $y)), '0');
		if ($event->until_time > $t) {
			$event->ftime .= ' - ';
			list ($h, $m, $s) = split (':', $event->until_time);
			$event->ftime .= ltrim (strftime ('%I:%M %p', mktime ($h, $m, $s, $d, $m, $y)), '0');
		}
	}
	$dates[strftime ('%A, %B %e', strtotime ($event->date))][] = $event;
}

function siteevent_time_sort ($a, $b) {
	if ($a->time == $b->time) {
		return 0;
	}
	return ($a->time < $b->time) ? -1 : 1;
}

// sort and limit $dates
$count = 0;
foreach ($dates as $k => $v) {
	if ($count > 20) {
		unset ($dates[$k]);
		continue;
	}
	usort ($dates[$k], 'siteevent_time_sort');
	$count++;
}

echo template_simple ('list.spt', array ('list' => $dates, 'date' => false));

echo '<p>';
if (appconf ('ical_links')) {
	if (! empty ($parameters['category'])) {
		$cat = '?category=' . $parameters['category'];
	} else {
		$cat = '';
	}
	echo '<a href="' . site_prefix () . '/index/siteevent-ical-action' . $cat . '">' . intl_get ('Subscribe (iCalendar)') . '</a> &nbsp; &nbsp; &nbsp; &nbsp;';
}
if (appconf ('rss_links')) {
	if (! empty ($parameters['category'])) {
		$cat = '?category=' . $parameters['category'];
	} else {
		$cat = '';
	}
	echo '<a href="' . site_prefix () . '/index/siteevent-rss-action' . $cat . '">' . intl_get ('Subscribe (RSS)') . '</a> &nbsp; &nbsp; &nbsp; &nbsp;';
}
echo '</p>';

/*
$today = date ('Y-m-d');
$dates = array ();
foreach ($list as $k => $event) {
	$day = $event->date;
	$dd = strftime ('%A, %B %e', strtotime ($event->date));

	if ($event->time == '00:00:00') {
			$event->time = false;
	} else {
			list ($h, $m, $s) = split (':', $event->time);
			$t = $event->time;
			$event->ftime = ltrim (strftime ('%I:%M %p', mktime ($h, $m, $s, $d, $m, $y)), '0');
			if ($event->until_time > $t) {
					$event->ftime .= ' - ';
					list ($h, $m, $s) = split (':', $event->until_time);
					$event->ftime .= ltrim (strftime ('%I:%M %p', mktime ($h, $m, $s, $d, $m, $y)), '0');
			}
	}

	switch ($event->recurring) {
		case 'yearly':
			$cur = $day;
			while ($cur <= $event->until_date) {
				if ($cur >= $today) {
					$dates[strftime ('%A, %B %e', strtotime ($cur))][] = $event;
					break;
				}
			}
			break;
		case 'monthly':
			$cur = $day;
			$count = 0;
			while ($cur <= $event->until_date && $count < 3) {
				if ($cur >= $today) {
					$dates[strftime ('%A, %B %e', strtotime ($cur))][] = $event;
					$count++;
				}
				$cur = Date::add ($cur, '1 month');
			}
			break;
		case 'weekly':
			$cur = $day;
			$count = 0;
			while ($cur <= $event->until_date && $count < 12) {
				if ($cur >= $today) {
					$dates[strftime ('%A, %B, %e', strtotime ($cur))][] = $event;
					$count++;
				}
				$cur = Date::add ($cur, '1 week');
			}
			break;
		case 'daily':
		case 'no':
		default:
			if ($event->until_date > $today) {
				$cur = $day;
				while ($cur <= $event->until_date) {
					if ($cur >= $today) {
						$dates[strftime ('%A, %B %e', strtotime ($cur))][] = $event;
					}
					$cur = Date::add ($cur, '1 day');
				}
			} else {
				$dates[$dd][] = $event;
			}
			break;
	}
}

function siteevent_time_sort ($a, $b) {
	if ($a->time == $b->time) {
		return 0;
	}
	return ($a->time < $b->time) ? -1 : 1;
}

// sort and limit $dates
$count = 0;
foreach ($dates as $k => $v) {
	if ($count > 20) {
		unset ($dates[$k]);
		continue;
	}
	usort ($dates[$k], 'siteevent_time_sort');
	$count++;
}

echo template_simple ('list.spt', array ('list' => $dates, 'date' => false));
*/

?>
