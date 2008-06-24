<?php

$keywords = array ();
$selected = preg_split ('/, ?/', $parameters['sel']);

foreach (db_shift_array ('select * from sitellite_keyword order by word asc') as $k) {
	$sel = in_array ($k, $selected) ? true : false;
	$keywords[] = array (
		'name' => $k,
		'sel' => $sel,
	);
}

page_title ('Global Keywords');

loader_import ('saf.GUI.Prompt');

echo template_simple (
	'keywords.spt',
	array (
		'keywords' => $keywords,
		'el' => $parameters['el'],
		'sel' => $parameters['sel'],
	)
);

?>