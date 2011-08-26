<?php

loader_import ('siteevent.Event');
loader_import ('saf.Date.Calendar.Mini');

global $cgi;

if (! isset ($parameters['minical']) || empty ($parameters['minical'])) {
	if (isset ($cgi->minical)) {
		$parameters['minical'] = $cgi->minical;
	} else {
		$parameters['minical'] = date ('Y-m');
	}
}

if (! isset ($parameters['category']) || empty ($parameters['category'])) {
	$parameters['category'] = '';
}

if (! isset ($parameters['audience']) || empty ($parameters['audience'])) {
	$parameters['audience'] = '';
}

if (! isset ($parameters['user']) || empty ($parameters['user'])) {
	$parameters['user'] = '';
}

// if date is past one year from present, tell robots to skip
$cy = date ('Y');
$cm = date ('m');
list ($y, $m) = split ('-', $parameters['minical']);
if ($y > ($cy + 1) || $y < ($cy - 1) || ($y == ($cy + 1) && $m >= $cm) || ($y == ($cy - 1) && $m <= $cm)) {
	page_add_meta ('robots', 'noindex,nofollow');
}

$cal = new MiniCal ($parameters['minical']);

$e = new SiteEvent_Event;

$list = $e->getMonthly ($parameters['minical'], $parameters['category'], $parameters['audience'], $parameters['user'], 'date, until_date, recurring, title, short_title');

foreach (array_keys ($list) as $k) {
	$item =& $list[$k];

	list ($y, $m, $d) = split ('-', $item->date);
	list ($yy, $mm, $dd) = split ('-', $item->until_date);

    if (!empty ($item->short_title)) {
        $item->title = $item->short_title;
    }

	switch ($item->recurring) {
		case 'yearly':
			if ($m == $cal->month) {
				$cal->addLink (
					$d,
					site_prefix () . '/index/siteevent-app/view.day/day.' . $parameters['minical'] . '-' . str_pad ($d, 2, '0', STR_PAD_LEFT),
					$item->title
				);
			}
			break;
		case 'monthly':
			$cal->addLink (
				$d,
				site_prefix () . '/index/siteevent-app/view.day/day.' . $parameters['minical'] . '-' . str_pad ($d, 2, '0', STR_PAD_LEFT),
				$item->title
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
					site_prefix () . '/index/siteevent-app/view.day/day.' . $parameters['minical'] . '-' . str_pad ($i, 2, '0', STR_PAD_LEFT),
					$item->title
				);
			}
			break;
			/*
			if ($yy == '0000' || $item->date == $item->until_date) {
				$cal->addLink (
					$d,
					site_prefix () . '/index/siteevent-app/view.day/day.' . $parameters['minical'] . '-' . $d
				);
				break;
			}
			*/
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
					site_prefix () . '/index/siteevent-app/view.day/day.' . $parameters['minical'] . '-' . str_pad ($i, 2, '0', STR_PAD_LEFT),
					$item->title
				);
			}
			break;
	}
/*
	if ($yy != '0000' && $d != $dd) {
		for ($i = $d; $i <= $dd; $i++) {
			$cal->addLink (
				$i,
				site_prefix () . '/index/siteevent-day-action/day.' . $parameters['minical'] . '-' . $i
			);
		}
	} else {
		$cal->addLink (
			$d,
			site_prefix () . '/index/siteevent-day-action/day.' . $parameters['minical'] . '-' . $d
		);
	}
*/
}

if (appconf ('css_location')) {
	$css = join ('', file (preg_replace ('|^' . site_prefix () . '/|', '', appconf ('css_location'))));
	echo '<style type="text/css">' . $css . '</style>';
}
echo $cal->render ();

?>