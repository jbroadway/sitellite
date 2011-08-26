<?php

$report = db_single (
	'select * from myadm_report where id = ?',
	$parameters['id']
);

if (! $report) {
	header ('Location: ' . site_prefix () . '/index/cms-browse-action?collection=myadm_report');
	exit;
}

$report->versions = db_fetch_array (
	'select * from myadm_report_results where report_id = ? order by run desc',
	$parameters['id']
);

if (count ($report->versions) == 0 || $parameters['ver'] == 'new') {
	$split = sql_split ($report->sql_query);
	$results = array ();
	foreach ($split as $i => $sql) {
		if (! empty ($sql)) {
			$results[$sql] = array (
				'data' => db_fetch_array ($sql),
				'error' => false,
				'rows' => 0,
			);
			$results[$sql]['rows'] = count ($results[$sql]['data']);
			$results[$sql]['headers'] = array ();
			if ($results[$sql]['rows'] > 0) {
				foreach ($results[$sql]['data'][0] as $k => $v) {
					$results[$sql]['headers'][] = $k;
				}
			}
			if (db_error ()) {
				$results[$sql]['error'] = db_error ();
			}
		}
	}
	db_execute (
		'insert into myadm_report_results values (null, ?, now(), ?)',
		$parameters['id'],
		serialize ($results)
	);
	$id = db_lastid ();
	header ('Location: ' . site_prefix () . '/index/myadm-report-action?id=' . $parameters['id'] . '&ver=' . $id);
	exit;
}

loader_import ('myadm.Filters');

if (! isset ($parameters['ver'])) {
	page_title (intl_get ('Report') . ': ' . $report->name);
	echo template_simple ('report_summary.spt', $report);
	return;
}

$report->ver = $parameters['ver'];
$report->run = false;
foreach ($report->versions as $k => $v) {
	if ($v->id == $parameters['ver']) {
		$report->run = $v->run;
		$report->results = unserialize ($v->results);
		break;
	}
}

if (! $report->run) {
	page_title ('Error');
	echo template_simple ('report_nodata.spt', $report);
}

page_add_style (site_prefix () . '/inc/app/myadm/js/shCore.css');
page_add_style (site_prefix () . '/inc/app/myadm/js/shThemeDefault.css');
page_add_style (site_prefix () . '/js/jquery.tooltip.css');
page_add_style (site_prefix () . '/inc/app/myadm/js/thickbox.css');
page_add_script (site_prefix () . '/inc/app/myadm/js/shCore.js');
page_add_script (site_prefix () . '/inc/app/myadm/js/shBrushSql.js');
//page_add_script (site_prefix () . '/js/jquery-1.2.3.min.js');
page_add_script (site_prefix () . '/js/jquery.dimensions.min.js');
page_add_script (site_prefix () . '/js/jquery.bgiframe.js');
page_add_script (site_prefix () . '/js/jquery.tooltip.min.js');
page_add_script (site_prefix () . '/inc/app/myadm/js/thickbox-compressed.js');

page_title (intl_get ('Report') . ': ' . $report->name);
echo template_simple ('report.spt', $report);
//info ($report->results);
return;

?>