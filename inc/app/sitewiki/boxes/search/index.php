<?php

page_title (intl_get ('Search'));

loader_import ('saf.Misc.Search');
loader_import ('sitewiki.Filters');

global $cgi;

$q = search_split_query ($cgi->query);
$j = ' ';
$w = '(';
$b = array ();

foreach ($q as $term) {
	$w .= $j . 'body like ?';
	$b[] = '%' . $term . '%';
	$j = ' AND ';
}

$w .= ')';

$res = db_shift_array (
	'select id from sitewiki_page where ' . $w,
	$b
);

if (count ($res) == 0) {
	echo template_simple ('nav.spt', new StdClass ());
	echo '<p>0 results for "' . $cgi->query . '"</p>';
	return;
} elseif (count ($res) == 1) {
	header ('Location: ' . site_prefix () . '/index/sitewiki-app/show.' . $res[0]);
	exit;
}

echo template_simple (
	'search.spt',
	(object) array (
		'total' => count ($res),
		'query' => $cgi->query,
		'list' => $res,
		'screen' => 'search',
	)
);

?>