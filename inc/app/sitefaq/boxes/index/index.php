<?php

if ($box['context'] == 'action') {
	page_title (appconf ('title'));
}

$res = 	db_fetch_array ('select * from sitefaq_question order by category asc, question asc');

$cats = array ();

foreach (array_keys ($res) as $k) {
	if (! isset ($cats[$res[$k]->category])) {
		$cats[$res[$k]->category] = array ();
	}
	$cats[$res[$k]->category][] = $res[$k];
}

echo template_simple (
	'faq.spt',
	array (
		'list' => $cats,
		'toplinks' => $parameters['toplinks'],
	)
);

?>