<?php

/**
 * Determines the date range for getTopSearches().
 */
function sitelinks_get_range ($range, $date) {
	switch ($range) {
		case 'day':
			return array (
				$date . ' 00:00:00',
				$date . ' 23:59:59',
			);
			break;
		/*case 'week':
			loader_import ('saf.Date');
			return array (
				'',
				'',
			);
			break;*/
		case 'month':
			loader_import ('saf.Date');
			list ($y, $m, $d) = explode ('-', $date);
			return array (
				$y . '-' . $m . '-01 00:00:00',
				$y . '-' . $m . '-' . Date::format ($date, 't') . ' 23:59:59',
			);
			break;
		case 'year':
			loader_import ('saf.Date');
			list ($y, $m, $d) = explode ('-', $date);
			return array (
				$y . '-01-01 00:00:00',
				$y . '-12-' . Date::format ($y . '-12-01', 't') . ' 23:59:59',
			);
			break;
	}
}

/**
 * Determines the previous and next date periods.
 */
function sitelinks_get_dates ($range, $date) {
	loader_import ('saf.Date');
	return array (
		Date::subtract ($date, '1 ' . $range),
		Date::add ($date, '1 ' . $range),
	);
}

function sitelinks_timezone ($offset) {
	$out = $offset[0];
	$offset = substr ($offset, 1);
	$h = floor ($offset / 3600);
	$m = floor (($offset % 3600) / 60);
	return $out . str_pad ($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad ($m, 2, '0', STR_PAD_LEFT);
}

?>