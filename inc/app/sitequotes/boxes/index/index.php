<?php

/* offset en limit goed zetten */
if (! $parameters['limit']) {
	$parameters['limit'] = 2;
}

if (! $parameters['offset']) {
	$parameters['offset'] = 0;
}

$countquery = db_fetch_array (
		'select * from sitequotes_entry'
		);
$parameters['total'] = count($countquery);

$res = db_fetch_array (
		'select * from sitequotes_entry LIMIT '.$parameters['offset'].','.$parameters['limit']
		);

if ($context == 'action') {
	page_title (appconf ('title'));
}

/* PAGER GEBEUREN.. */
loader_import ('saf.GUI.Pager');

$pg = new Pager ($parameters['offset'], $parameters['limit'], $parameters['total']);
$pg->getInfo ();
$pg->setUrl (site_prefix () . '/index/sitequotes-app?');
// $pg->setUrl (site_prefix () . '/index/sitequotes-app?limits=' . $parameters['limits']);
template_simple_register ('pager', $pg);

echo template_simple (
	'list.spt',
	array (
		'list' => $res,
	)
);

?>