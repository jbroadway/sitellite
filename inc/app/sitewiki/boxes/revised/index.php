<?php

page_title (intl_get ('Recently Revised'));

$res = db_fetch_array (
	'select distinct id from sitewiki_page_sv order by sv_autoid desc limit 10'
);

foreach (array_keys ($res) as $k) {
	$res[$k] = db_single (
		'select id, sv_author, sv_revision from sitewiki_page_sv where id = ? order by sv_autoid desc limit 1',
		$res[$k]->id
	);
	if ($res[$k]->sv_author == 'system') {
		$res[$k]->sv_author = 'anonymous';
	}
}

loader_import ('sitewiki.Filters');

$data = new StdClass ();
$data->list = $res;
$data->screen = 'revised';

echo template_simple (
	'revised.spt',
	$data
);

?>