<?php

if ($box['context'] == 'action') {
	page_title (appconf ('title'));
}

if (empty ($parameters['category'])) {
	$parameters['category'] = db_shift ('select name from siteglossary_category order by name asc limit 1');
}

$res = 	db_fetch_array ('select * from siteglossary_term where category = ? order by word asc', $parameters['category']);

echo template_simple (
	'category.spt',
	array (
		'list' => $res,
	)
);

?>