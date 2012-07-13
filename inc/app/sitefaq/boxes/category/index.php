<?php

if ($box['context'] == 'action') {
	page_title (appconf ('title'));
}

if (empty ($parameters['category'])) {
	$parameters['category'] = db_shift ('select name from sitefaq_category order by name asc limit 1');
}

$res = 	db_fetch_array ('select * from sitefaq_question where category = ? order by question asc', $parameters['category']);

page_add_style ( site_prefix () . '/inc/app/sitefaq/html/faq.css' );

echo template_simple (
	'category.spt',
	array (
		'list' => $res,
	)
);

?>