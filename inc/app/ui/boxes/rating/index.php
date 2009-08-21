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
if (!isset ($parameters['anon'])) {
	$parameters['anon'] = 'no';
}
if (!isset ($parameters['readonly'])) {
	$parameters['readonly'] = 'yes';
}

if ( ! session_valid ()) {
	if ($parameters['anon'] == 'yes') {
		$username = $_SERVER["REMOTE_ADDR"];
	}
	else {
		// if user not logged and no anonymous rating,
		// show average rating instead
		$parameters['readonly'] = 'yes';
	}
}
else {
	$username = session_username ();
}

if ($parameters['readonly'] == 'yes') {
	$nstars = $parameters['nstars'] * 2;
	$options = array (
			'disabled' => 'true',
			'split' => 2);

	// Get current value
	$value = db_shift ('SELECT AVG(rating) FROM ui_rating
			WHERE `group`=? AND item=? GROUP BY item',
			$parameters['group'], $parameters['item']);
	$value = round ($value * 2);
}
else {
	// Add AJAX scripts
	page_add_script (site_prefix () . '/js/rpc-compressed.js');
	page_add_script (site_prefix () . '/inc/app/ui/js/rpc.rating.js');

	$options = array (
			'callback' => 'function(ui, type, value){' . 
			"rating.set('{$parameters['group']}', '{$parameters['item']}', '{$username}', value);}");

	// Get current value
	$value = db_shift ('SELECT rating FROM ui_rating
			WHERE `group`=? AND item=? and user=?',
			$parameters['group'], $parameters['item'], $username);
}

// Create Widget
$name = $parameters['group'] . '-stars';
$stars = new MF_Widget_rating ($name);
$stars->setValues ( range (0, $parameters['nstars'], 1) );
$stars->setValue ($value);
$stars->starOptions = $options;
echo $stars->display (false);

if (! session_valid () && $parameters['anon'] == 'no') {
	echo '<a href="' . site_prefix () . '/sitemember-login-action' . intl_get ('Sign in to rate.') . '</a>';
}
else if ($parameters['readonly'] == 'no') {
	echo '<br /><p id="ui-ratings-text" style="display: none;">&nbsp;</p>';
}


?>
