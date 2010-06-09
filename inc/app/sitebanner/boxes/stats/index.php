<?php

if (! session_admin ()) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

loader_import ('saf.Date');
loader_import ('cms.Versioning.Rex');
loader_import ('sitebanner.Filters');

global $cgi;

if (! isset ($cgi->_range)) {
	$cgi->_range = 'week';
}

if (! isset ($cgi->date)) {
	$cgi->date = date ('Y-m-d');
}

if (! isset ($cgi->id)) {
	header ('Location: ' . site_prefix () . '/index/cms-browse-action?collection=sitebanner_ad');
	exit;
}

$data = array ();

$rex = new Rex (false);

$rex->facets['range'] = new rSelectFacet ('range', array ('display' => intl_get ('Date Range'), 'type' => 'select'));
$rex->facets['range']->preserve = array ('date', 'id');
$rex->facets['range']->options = array (
	'day' => intl_get ('Day'),
	'week' => intl_get ('Week'),
	'month' => intl_get ('Month'),
	'year' => intl_get ('Year'),
);
$rex->facets['range']->count = false;
$rex->facets['range']->all = false;

$data['facets'] = $rex->renderFacets (1);
$data['bookmark'] = true;

$data['date'] = $cgi->date;
$data['previous'] = Date::subtract ($cgi->date, '1 ' . $cgi->_range);
$data['next'] = Date::add ($cgi->date, '1 ' . $cgi->_range);

switch ($cgi->_range) {
	case 'day':
		$parts = array (
			'Midnight-1am' => array ($cgi->date . ' 00:00:00', $cgi->date . ' 00:59:59'),
			'1:00-2:00' => array ($cgi->date . ' 01:00:00', $cgi->date . ' 01:59:59'),
			'2:00-3:00' => array ($cgi->date . ' 02:00:00', $cgi->date . ' 02:59:59'),
			'3:00-4:00' => array ($cgi->date . ' 03:00:00', $cgi->date . ' 03:59:59'),
			'4:00-5:00' => array ($cgi->date . ' 04:00:00', $cgi->date . ' 04:59:59'),
			'5:00-6:00' => array ($cgi->date . ' 05:00:00', $cgi->date . ' 05:59:59'),
			'6:00-7:00' => array ($cgi->date . ' 06:00:00', $cgi->date . ' 06:59:59'),
			'7:00-8:00' => array ($cgi->date . ' 07:00:00', $cgi->date . ' 07:59:59'),
			'8:00-9:00' => array ($cgi->date . ' 08:00:00', $cgi->date . ' 08:59:59'),
			'9:00-10:00' => array ($cgi->date . ' 09:00:00', $cgi->date . ' 09:59:59'),
			'10:00-11:00' => array ($cgi->date . ' 10:00:00', $cgi->date . ' 10:59:59'),
			'11am-Noon' => array ($cgi->date . ' 11:00:00', $cgi->date . ' 11:59:59'),
			'Noon-1:00' => array ($cgi->date . ' 12:00:00', $cgi->date . ' 12:59:59'),
			'1:00-2:00 ' => array ($cgi->date . ' 13:00:00', $cgi->date . ' 13:59:59'),
			'2:00-3:00 ' => array ($cgi->date . ' 14:00:00', $cgi->date . ' 14:59:59'),
			'3:00-4:00 ' => array ($cgi->date . ' 15:00:00', $cgi->date . ' 15:59:59'),
			'4:00-5:00 ' => array ($cgi->date . ' 16:00:00', $cgi->date . ' 16:59:59'),
			'5:00-6:00 ' => array ($cgi->date . ' 17:00:00', $cgi->date . ' 17:59:59'),
			'6:00-7:00 ' => array ($cgi->date . ' 18:00:00', $cgi->date . ' 18:59:59'),
			'7:00-8:00 ' => array ($cgi->date . ' 19:00:00', $cgi->date . ' 19:59:59'),
			'8:00-9:00 ' => array ($cgi->date . ' 20:00:00', $cgi->date . ' 20:59:59'),
			'9:00-10:00 ' => array ($cgi->date . ' 21:00:00', $cgi->date . ' 21:59:59'),
			'10:00-11:00 ' => array ($cgi->date . ' 22:00:00', $cgi->date . ' 22:59:59'),
			'11pm-Midnight' => array ($cgi->date . ' 23:00:00', $cgi->date . ' 23:59:59'),
		);
		break;
	case 'week':
		$day = strtolower (date ('D', strtotime ($cgi->date)));
		$days = array ('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
		$key = array_search ($day, $days);
		if ($key === false) {
			return;
		}
		$orig = $key;

		loader_import ('saf.Date');

		while ($key >= 0) {
			$minus = $orig - $key;

			if ($orig == $key) {
				${'_' . $day} = $cgi->date;
			} else {
				${'_' . $days[$key]} = Date::subtract ($cgi->date, abs ($minus) . ' days');
			}

			$key--;
		}

		$key = $orig;
		$c = 0;
		while ($key <= 6) {
			$add = $key - $orig;

			if ($orig != $key) {
				${'_' . $days[$key]} = Date::add ($cgi->date, $add . ' days');
			}

			$key++;
		}

		$parts = array (
			intl_get ('Sunday') => array ($_sun . ' 00:00:00', $_sun . ' 23:59:59'),
			intl_get ('Monday') => array ($_mon . ' 00:00:00', $_mon . ' 23:59:59'),
			intl_get ('Tuesday') => array ($_tue . ' 00:00:00', $_tue . ' 23:59:59'),
			intl_get ('Wednesday') => array ($_wed . ' 00:00:00', $_wed . ' 23:59:59'),
			intl_get ('Thursday') => array ($_thu . ' 00:00:00', $_thu . ' 23:59:59'),
			intl_get ('Friday') => array ($_fri . ' 00:00:00', $_fri . ' 23:59:59'),
			intl_get ('Saturday') => array ($_sat . ' 00:00:00', $_sat . ' 23:59:59'),
		);
		break;
	case 'month':
		list ($y, $m, $d) = split ('-', $cgi->date);
		if ($y . '-' . $m == date ('Y-m')) {
			$days = date ('d');
		} else {
			$days = date ('t', strtotime ($cgi->date));
		}
		$parts = array ();
		for ($i = 1; $i <= $days; $i++) {
			$parts[date ('jS', strtotime ($y . '-' . $m . '-' . str_pad ($i, 2, '0', STR_PAD_LEFT)))] = array (
				$y . '-' . $m . '-' . str_pad ($i, 2, '0', STR_PAD_LEFT) . ' 00:00:00',
				$y . '-' . $m . '-' . str_pad ($i, 2, '0', STR_PAD_LEFT) . ' 23:59:59',
			);
		}
		break;
	case 'year':
		list ($y, $m, $d) = split ('-', $cgi->date);
		$parts = array (
			intl_get ('January') => array ($y . '-01-01 00:00:00', $y . '-01-31 23:59:59'),
			intl_get ('February') => array ($y . '-02-01 00:00:00', $y . '-02-31 23:59:59'),
			intl_get ('March') => array ($y . '-03-01 00:00:00', $y . '-03-31 23:59:59'),
			intl_get ('April') => array ($y . '-04-01 00:00:00', $y . '-04-31 23:59:59'),
			intl_get ('May') => array ($y . '-05-01 00:00:00', $y . '-05-31 23:59:59'),
			intl_get ('June') => array ($y . '-06-01 00:00:00', $y . '-06-31 23:59:59'),
			intl_get ('July') => array ($y . '-07-01 00:00:00', $y . '-07-31 23:59:59'),
			intl_get ('August') => array ($y . '-08-01 00:00:00', $y . '-08-31 23:59:59'),
			intl_get ('September') => array ($y . '-09-01 00:00:00', $y . '-09-31 23:59:59'),
			intl_get ('October') => array ($y . '-10-01 00:00:00', $y . '-10-31 23:59:59'),
			intl_get ('November') => array ($y . '-11-01 00:00:00', $y . '-11-31 23:59:59'),
			intl_get ('December') => array ($y . '-12-01 00:00:00', $y . '-12-31 23:59:59'),
		);
		break;
}

$data['parts'] = array ();
$data['views_total'] = 0;
$data['clicks_total'] = 0;
$data['ctr_total'] = 0;
$count = 0;

foreach ($parts as $k => $range) {
	$views = db_shift (
		'select count(*) from sitebanner_view where ts >= ? and ts <= ? and campaign = ?',
		$range[0],
		$range[1],
		$cgi->id
	);
	$clicks = db_shift (
		'select count(*) from sitebanner_click where ts >= ? and ts <= ? and campaign = ?',
		$range[0],
		$range[1],
		$cgi->id
	);
	$ctr = @number_format (($clicks / $views) * 100, 2);

	$data['parts'][] = array (
		'part' => $k,
		'from' => $range[0],
		'to' => $range[1],
		'views' => $views,
		'clicks' => $clicks,
		'ctr' => $ctr,
	);

	if ($views > 0) {
		$count++;
	}
	$data['views_total'] += $views;
	$data['clicks_total'] += $clicks;
	$data['ctr_total'] += $ctr;
}

$data['views_avg'] = @number_format (ceil ($data['views_total'] / $count), 0);
$data['clicks_avg'] = @number_format (ceil ($data['clicks_total'] / $count), 0);
$data['ctr_avg'] = @number_format ($data['ctr_total'] / $count, 2);

$data['ctr_total'] = @number_format ($data['ctr_total'], 2);

if ($cgi->csv == 'true') {
	$data['title'] = intl_get ('Banner Stats') . ': ' . db_shift (
		'select name from sitebanner_ad where id = ?',
		$cgi->id
	) . ', ' . sitebanner_filter_date ($cgi->date);

	header ('Content-Type: application/octet-stream');
	header ('Content-Disposition: attachment; filename="csv.txt"');
	echo template_simple ('stats_csv.spt', $data);
	exit;
} else {
	page_title (intl_get ('Banner Stats') . ': ' . db_shift (
		'select name from sitebanner_ad where id = ?',
		$cgi->id
	));

	page_template_set ('admin');
	echo template_simple ('stats.spt', $data);
}

?>