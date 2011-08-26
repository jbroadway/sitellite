<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #192 Test all config files for multilingual dates.

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

page_title (intl_get (appconf ('siteevent_title')));
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
//START: SEMIAS. #192 Test all config files for multilingual dates.
//-----------------------------------------------
//foreach ($list as $k => $event) {
//	$day = $event->date;
//	$dd = strftime ('%A, %B %e', strtotime ($event->date));
//
//	if ($event->time == '00:00:00') {
//		$event->time = false;
//	} else {
//		list ($h, $m, $s) = split (':', $event->time);
//		$t = $event->time;
//		$event->ftime = ltrim (strftime ('%I:%M %p', mktime ($h, $m, $s, $d, $m, $y)), '0');
//		if ($event->until_time > $t) {
//			$event->ftime .= ' - ';
//			list ($h, $m, $s) = split (':', $event->until_time);
//			$event->ftime .= ltrim (strftime ('%I:%M %p', mktime ($h, $m, $s, $d, $m, $y)), '0');
//		}
//	}
//	$dates[strftime ('%A, %B %e', strtotime ($event->date))][] = $event;
//}
//-----------------------------------------------
foreach ($list as $k => $event) {
	$day = $event->date;
	$dd = intl_date ($event->date, 'longevdate');

	if ($event->time == '00:00:00') {
		$event->time = false;
	} else {
		$t = $event->time;
		$event->ftime = intl_time ($event->time);
		if ($event->until_time > $t) {
			$event->ftime .= ' - ';
			$event->ftime .= intl_time ($event->until_time);
		}
	}
	$dates[$dd][] = $event;
}
//END: SEMIAS.

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
