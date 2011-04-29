<?php

if (! isset ($parameters['case'])) {
	page_title (intl_get ('Case Studies'));

	echo template_simple (
		'index.spt',
		db_fetch_array (
			'select * from sitestudy_item order by sort_weight desc, id desc'
		)
	);
} else {
	$res = db_single (
		'select * from sitestudy_item where id = ?',
		$parameters['case']
	);

	page_title (intl_get ('Case Study') . ': ' . $res->client);

	page_add_meta ('description', $res->description);
	page_add_meta ('keywords', $res->keywords);

	echo template_simple (
		'case.spt',
		$res
	);
}

?>