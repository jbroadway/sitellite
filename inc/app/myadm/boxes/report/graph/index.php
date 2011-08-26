<?php

page_template ('dialog');

if (! isset ($parameters['id'])) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('No report selected.') . '</p>';
	return;
}

if (! isset ($parameters['ver'])) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('No report date selected.') . '</p>';
	return;
}

if (! isset ($parameters['query'])) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('No query selected.') . '</p>';
	return;
}

if (! isset ($parameters['col'])) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('No column selected.') . '</p>';
	return;
}

$report = db_single (
	'select count(*) from myadm_report where id = ?',
	$parameters['id']
);

if (! $report) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('Report not found.') . '</p>';
	return;
}

$v = db_single (
	'select * from myadm_report_results where report_id = ? and id = ?',
	$parameters['id'],
	$parameters['ver']
);
if (! $v) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('Report data not found.') . '</p>';
	return;
}
$results = unserialize ($v->results);

if (count ($results) < $parameters['query']) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('Query not found.') . '</p>';
	return;
}

$i = 1;
foreach ($results as $res) {
	if ($i == $parameters['query']) {
		$query = $res['data'];
		$colname = $res['headers'][$parameters['col'] - 1];
		break;
	}
	$i++;
}
$col = $parameters['col'] - 1;

$data = array ();
$labels = array ();
$bar_width = ceil (400 / count ($query));
if ($bar_width < 1) {
	$bar_width = 1;
}

$i = 1;
foreach ($query as $q) {
	$data[] = $q->{$colname};
	$labels[] = $i;
	$i++;
}

$data = '[' . join (',', $data) . ']';
$labels = '[' . join (',', $labels) . ']';

//page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');
page_add_script (site_prefix () . '/inc/app/myadm/js/jgcharts.pack.js');

echo template_simple ('report_chart.spt', array (
	'data' => $data,
	'labels' => $labels,
	'bar_width' => $bar_width,
));

/*info ($col);
info ($colname);
info ($data);
info ($labels);
info ($bar_width);
info ($query);*/

?>