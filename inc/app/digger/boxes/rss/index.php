<?php

loader_import('digger.Functions');
loader_import('digger.Filters');

$res = db_fetch_array(
	'SELECT * FROM digger_linkstory
	WHERE status = "enabled"
	ORDER BY posted_on desc, score desc
	LIMIT 10'
);

header('Content-Type: text/xml');
echo template_simple('rss.spt',
	array(
		'list' => $res,
		'rss_title' => appconf('digger_title'),
		'rss_date' => date('Y-m-d\TH:i:s') . digger_timezone(date('Z'))
	)
);
exit;

?>