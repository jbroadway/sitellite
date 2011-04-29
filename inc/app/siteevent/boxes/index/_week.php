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

if (! $parameters['week']) {
	$parameters['week'] = date ('Y-m-d');
}

$list = $e->getWeek ($parameters['week'], $parameters['category'], $parameters['audience'], $parameters['user'], 'id, title, short_title, date, time, until_date, until_time, priority, recurring');

$list =& siteevent_translate ($list);

loader_import ('saf.Date');

$cur = date ('w', strtotime ($parameters['week']));
if ($cur > 0) {
	$week_of = Date::subtract ($parameters['week'], $cur . ' day');
} else {
	$week_of = $parameters['week'];
}
$until = Date::add ($week_of, '6 day');

$items = array ();

for ($i = $week_of; $i <= $until; $i = Date::add ($i, '1 day')) {
	$items[$i] = array ();
}

$cal = new StdClass;
list ($cal->year, $cal->month, $cal->day) = explode ('-', $week_of);
//START: SEMIAS. #192 Test all config files for multilingual dates.
//-----------------------------------------------
/*
foreach (array_keys ($list) as $k) {
	$item =& $list[$k];

	$title = (! empty ($item->short_title)) ? $item->short_title : $item->title;
	$priority = ($item->priority == 'high') ? true : false;
	$alt = $item->title;
	if ($item->time > '00:00:00') {
		$alt .= ' - ' . Date::time ($item->time, 'g:i A');
	}
	if ($item->end_time > '00:00:00') {
		$alt .= ' - ' . Date::time ($item->end_time, 'g:i A');
	}

	$item->_time = $item->time;

	if ($item->time == '00:00:00') {
		$item->time = '';
	} else {
		list ($h, $m, $s) = split (':', $item->time);
		$t = $item->time;
		$item->time = ltrim (strftime ('%I:%M %p', mktime ($h, $m, $s, $d, $mm, $y)), '0');
		if ($item->until_time > $t) {
			$item->time .= ' - ';
			list ($h, $m, $s) = split (':', $item->until_time);
			$item->time .= ltrim (strftime ('%I:%M %p', mktime ($h, $m, $s, $d, $mm, $y)), '0');
		}
	}
	$item->time = str_replace (':00', '', $item->time);
	if (substr_count ($item->time, 'AM') > 1) {
		$item->time = str_replace (' AM ', ' ', $item->time);
	}
	if (substr_count ($item->time, 'PM') > 1) {
		$item->time = str_replace (' PM ', ' ', $item->time);
	}
*/
//-----------------------------------------------
foreach (array_keys ($list) as $k) {
	$item =& $list[$k];

	$title = (! empty ($item->short_title)) ? $item->short_title : $item->title;
	$priority = ($item->priority == 'high') ? true : false;
	$alt = $item->title;
	if ($item->time > '00:00:00') {
		$alt .= ' - ' . intl_time ($item->time);
	}
	if ($item->end_time > '00:00:00') {
		$alt .= ' - ' . intl_time ($item->end_time);
	}

	$item->_time = $item->time;

	if ($item->time == '00:00:00') {
		$item->time = '';
	} else {
		$t = $item->time;
		$item->time = intl_time ($item->time);
		if ($item->until_time > $t) {
			$item->time .= ' - ';
			$item->time .= intl_time ($item->until_time);
		}
	}
//END: SEMIAS.
	list ($y, $m, $d) = explode ('-', $item->date);
	list ($yy, $mm, $dd) = explode ('-', $item->until_date);

	switch ($item->recurring) {
		case 'yearly':
			$year = substr ($item->date, 0, 4);
			$until = substr ($item->until_date, 0, 4);
			if (! $until) {
				$until = $year;
			}
			for ($i = $year; $i <= $until; $i++) {
				$ydate = preg_replace ('/^[0-9]+/', $i, $item->date);
				if (isset ($items[$ydate])) {
					$items[$ydate][] = array (
						'id' => $item->id,
						'title' => $title,
						'link' => site_prefix () . '/index/siteevent-details-action/id.' . $item->id . '/title.' . siteevent_filter_link_title ($item->title),
						'priority' => $item->priority,
						'alt' => $alt,
						'time' => $item->time,
					);
					break;
				}
			}
			break;
		case 'monthly':
			$mdate = $item->date;
			if (! $item->until_date || $item->until_date == '0000-00-00') {
				$item->until_date = $item->date;
			}
			while ($mdate <= $item->until_date) {
				if (isset ($items[$mdate])) {
					$items[$mdate][] = array (
						'id' => $item->id,
						'title' => $title,
						'link' => site_prefix () . '/index/siteevent-details-action/id.' . $item->id . '/title.' . siteevent_filter_link_title ($item->title),
						'priority' => $item->priority,
						'alt' => $alt,
						'time' => $item->time,
					);
					break;
				}
				$mdate = Date::add ($mdate, '1 month');
			}
			break;
		case 'weekly':
			$w = date ('w', mktime (5, 0, 0, $m, $d, $y)) + 1;
			$ws = date ('w', mktime (5, 0, 0, $cal->month, 1, $cal->year)) + 1;

			if ($y . '-' . $m < $cal->year . '-' . $cal->month) {
				$start = 1 + ($w - $ws);
			} else {
				$start = $d;
			}
			if ($yy == '0000' || $yy . '-' . $mm > $cal->year . '-' . $cal->month) {
				$end = date ('t', mktime (5, 0, 0, $cal->month, 1, $cal->year));
			} else {
				$end = $dd;
			}

			for ($i = $start; $i <= $end; $i += 7) {
				$date = $cal->year . '-' . $cal->month . '-' . str_pad ($i, 2, '0', STR_PAD_LEFT);
				if (isset ($items[$date])) {
					$items[$date][] = array (
						'id' => $item->id,
						'title' => $title,
						'link' => site_prefix () . '/index/siteevent-details-action/id.' . $item->id . '/title.' . siteevent_filter_link_title ($item->title),
						'priority' => $item->priority,
						'alt' => $alt,
						'time' => $item->time,
					);
					break;
				}
			}
			break;
		case 'daily':
		case 'no':
		default:
			if (empty ($item->until_date) || $item->until_date == '0000-00-00') {
				if (isset ($items[$item->date])) {
					$items[$item->date][] = array (
						'id' => $item->id,
						'title' => $title,
						'link' => site_prefix () . '/index/siteevent-details-action/id.' . $item->id . '/title.' . siteevent_filter_link_title ($item->title),
						'priority' => $item->priority,
						'alt' => $alt,
						'time' => $item->time,
					);
				}
				break;
			}

			$first = $item->date;
			$last = $item->until_date;
			$days = array ();
			for ($i = $first; $i <= $last; $i = Date::add ($i, '1 day')) {
				if (isset ($items[$i])) {
					$items[$i][] = array (
						'id' => $item->id,
						'title' => $title,
						'link' => site_prefix () . '/index/siteevent-details-action/id.' . $item->id . '/title.' . siteevent_filter_link_title ($item->title),
						'priority' => $item->priority,
						'alt' => $alt,
						'time' => $item->time,
					);
				}
			}
			break;
	}
}

foreach ($items as $d => $links) {
	usort ($items[$d], 'siteevent_time_sort');
}

if (false && session_admin ()) {
	echo loader_box ('cms/buttons/add', array ('collection' => 'siteevent_event', 'float' => true));
	echo '<br clear="all" />';

	echo template_simple (
		'users.spt',
		array (
			'list' => db_fetch_array ('select * from siteevent_category order by name asc'),
			'current' => $parameters['category'],
			'user_list' => db_fetch_array ('select sitellite_owner, count(*) as total from siteevent_event where ' . session_allowed_sql () . ' group by sitellite_owner asc'),
			'current_user' => $parameters['user'],
			'simplecal' => $parameters['simplecal'],
		)
	);
} else {
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
}

echo template_simple (
	'week.spt',
	array (
		'list' => $items,
		'date' => date ('Y-m-d'),
		'dateName' => intl_get ('Week of') . ' ' . intl_date ($week_of, 'cevdate'),
		'prev' => Date::subtract ($week_of, '7 day'),
		'next' => Date::add ($week_of, '7 day'),
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

page_title (intl_get (appconf ('siteevent_title')));
if (appconf ('template_calendar')) {
	page_template (appconf ('template_calendar'));
} elseif (appconf ('template')) {
	page_template (appconf ('template'));
}

?>