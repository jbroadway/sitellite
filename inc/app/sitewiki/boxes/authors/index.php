<?php

page_title (intl_get ('Authors'));

loader_import ('sitewiki.Filters');

$res = db_shift_array (
	'select distinct sv_author from sitewiki_page_sv where sv_author != "" order by sv_author asc'
);

foreach ($res as $k => $v) {
	unset ($res[$k]);
	if ($v == 'system') {
		$v = 'anonymous';
		$res[$v] = db_shift_array (
			'select distinct id from sitewiki_page_sv where sv_author = ? and id != "" order by id asc',
			'system'
		);
	} else {
		$res[$v] = db_shift_array (
			'select distinct id from sitewiki_page_sv where sv_author = ? and id != "" order by id asc',
			$v
		);
	}
	foreach ($res[$v] as $key => $pg) {
		$res[$v][$key] = '<a href="' . site_prefix () . '/index/sitewiki-app/show.' . $pg . '">' . sitewiki_filter_id ($pg) . '</a>';
		if ($key < count ($res[$v]) - 1) {
			$res[$v][$key] .= ', ';
		}
	}
}

$data = new StdClass ();
$data->list = $res;
$data->screen = 'authors';

echo template_simple (
	'authors.spt',
	$data
);

?>