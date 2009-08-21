<?php

loader_import ('ui.Widget.Rating');

if (!isset ($parameters['group'])) {
	$parameters['group'] = 'sitellite_page';
}
if (!isset ($parameters['item'])) {
	$parameters['item'] = $GLOBALS['page']->id;
}
if (!isset ($parameters['nstars'])) {
	$parameters['nstars'] = 5;
}

$nstars = $parameters['nstars'] * 2;
$options = array ('disabled' => 'true', 'split' => 2);

// Get current value
$value = db_shift ('SELECT AVG(rating) FROM ui_rating
		WHERE `group`=? AND item=? GROUP BY item',
		$parameters['group'], $parameters['item']);
$value = round ($value * 2);

// Create Widget
$name = $parameters['group'] . '-show-stars';
$stars = new MF_Widget_rating ($name);
$stars->setValues ( range (0, $nstars, 1) );
$stars->setValue ($value);
$stars->starOptions = $options;
echo $stars->display (false);

?>
