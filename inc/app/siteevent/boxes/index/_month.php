<?php

$cal->dayLinks = '{site/prefix}/index/siteevent-app/view.day/day.{date}';

$list = $e->getMonthly ($parameters['simplecal'], $parameters['category'], $parameters['audience'], $parameters['user'], 'id, title, short_title, date, time, until_date, until_time, priority, recurring');

$list =& siteevent_translate ($list);

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

	list ($y, $m, $d) = explode ('-', $item->date);
	list ($yy, $mm, $dd) = explode ('-', $item->until_date);

	switch ($item->recurring) {
		case 'yearly':
			if ($m == $cal->month) {
				$cal->addLink (
					$d,
					$title,
					site_prefix () . '/index/siteevent-details-action/id.' . $item->id . '/title.' . siteevent_filter_link_title ($item->title),
					$priority,
					$alt,
					$item->time
				);
			}
			break;
		case 'monthly':
			$cal->addLink (
				$d,
				$title,
				site_prefix () . '/index/siteevent-details-action/id.' . $item->id . '/title.' . siteevent_filter_link_title ($item->title),
				$priority,
				$alt,
				$item->time
			);
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
				$cal->addLink (
					$i,
					$title,
					site_prefix () . '/index/siteevent-details-action/id.' . $item->id . '/title.' . siteevent_filter_link_title ($item->title),
					$priority,
					$alt,
					$item->time
				);
			}
			break;
		case 'daily':
		case 'no':
		default:
			if ($y . '-' . $m < $cal->year . '-' . $cal->month) {
				$start = 1;
			} else {
				$start = $d;
			}
			if (($item->recurring == 'daily' && $yy == '0000') || $yy . '-' . $mm > $cal->year . '-' . $cal->month) {
				$end = date ('t', mktime (5, 0, 0, $cal->month, 1, $cal->year));
			} elseif ($yy != '0000') {
				$end = $dd;
			} else {
				$end = $d;
			}

			for ($i = $start; $i <= $end; $i++) {
				$cal->addLink (
					$i,
					$title,
					site_prefix () . '/index/siteevent-details-action/id.' . $item->id . '/title.' . siteevent_filter_link_title ($item->title),
					$priority,
					$alt,
					$item->time
				);
			}
			break;
	}
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

echo $cal->render ();

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

page_title (appconf ('siteevent_title'));
if (appconf ('template_calendar')) {
	page_template (appconf ('template_calendar'));
} elseif (appconf ('template')) {
	page_template (appconf ('template'));
}

?>