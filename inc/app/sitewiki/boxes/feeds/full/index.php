<?php

loader_import ('sitewiki.Filters');

$res = db_fetch_array (
	'select distinct id from sitewiki_page_sv order by sv_autoid desc limit 10'
);

foreach (array_keys ($res) as $k) {
	$res[$k] = db_single (
		'select id, sv_author, sv_revision, body from sitewiki_page_sv where id = ? order by sv_autoid desc limit 1',
		$res[$k]->id
	);
	if ($res[$k]->sv_author == 'system') {
		$res[$k]->sv_author = 'anonymous';
	}
	$res[$k]->body = sitewiki_filter_body ($res[$k]->body);
}

$data = new StdClass ();
$data->list = $res;

$settings = ini_parse ('inc/app/sitewiki/conf/settings.php', false);
foreach ($settings as $k => $v) {
	$data->{$k} = $v;
}

header ('Content-Type: text/xml');

echo template_simple (
	'feed_full.spt',
	$data
);

exit;

?>