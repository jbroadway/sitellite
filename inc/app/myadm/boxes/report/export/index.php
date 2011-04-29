<?php

if (! isset ($parameters['id'])) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('No report selected.') . ' <a href="#" onclick="history.go (-1); return false">' . intl_get ('Back') . '</a></p>';
	return;
}

$report = db_single (
	'select * from myadm_report where id = ?',
	$parameters['id']
);

if (! $report) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('Report not found.') . ' <a href="#" onclick="history.go (-1); return false">' . intl_get ('Back') . '</a></p>';
	return;
}

if (! isset ($parameters['ver'])) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('No report date selected.') . ' <a href="#" onclick="history.go (-1); return false">' . intl_get ('Back') . '</a></p>';
	return;
}

$report->ver = $parameters['ver'];
$v = db_single (
	'select * from myadm_report_results where report_id = ? and id = ?',
	$parameters['id'],
	$parameters['ver']
);
if (! $v) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('Report data not found.') . ' <a href="#" onclick="history.go (-1); return false">' . intl_get ('Back') . '</a></p>';
	return;
}
$report->run = $v->run;
$report->results = unserialize ($v->results);

set_time_limit (0);

header ('Cache-control: private');
header ('Content-Type: text/plain');
header ('Content-Disposition: attachment; filename="' . $report->name . ' - ' . $report->run . '.csv"');

$i = 1;
foreach ($report->results as $sql => $query) {
	echo '----- Query ' . $i . ' (' . $query['rows'] . " results) -----\n";
	foreach ($query['headers'] as $k => $v) {
		$query['headers'][$k] = str_replace ('"', '""', $v);
		if (strpos ($v, ',') !== false) {
			$query['headers'][$k] = '"' . $v .'"';
		}
	}
	echo join (',', $query['headers']) . "\n";
	foreach ($query['data'] as $row) {
		$r = (array) $row;
		foreach (array_keys ($r) as $k) {
			$r[$k] = str_replace ('"', '""', $r[$k]);
			if (strpos ($r[$k], ',') !== false) {
				$r[$k] = '"' . $r[$k] . '"';
			}
		}
		echo str_replace (array ("\r", "\n"), array ('\\r', '\\n'), join (',', $r)) . "\n";
	}
	$i++;
}

//info ($report->results);
exit;

?>