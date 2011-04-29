<?php

page_title (intl_get ('All Pages'));

$data = new StdClass ();

$data->all = db_shift_array (
	'select distinct id from sitewiki_page where id != "" order by id asc'
);

$bodies = db_pairs (
	'select id, body from sitewiki_page where body regexp "(([A-Z][a-z0-9]+){2,})"'
);

$data->wanted = array ();

foreach ($bodies as $id => $body) {
	preg_match_all ('/(([A-Z][a-z0-9]+){2,})/s', $body, $regs, PREG_SET_ORDER);
	foreach ($regs as $reg) {
		if (! in_array ($reg[1], $data->all) && ! in_array ($reg[1], $data->wanted)) {
			$data->wanted[$reg[1]] = $id;
		}
	}
}

ksort ($data->wanted);

$data->screen = 'all';

loader_import ('sitewiki.Filters');

echo template_simple ('all.spt', $data);

?>