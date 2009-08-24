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
	$parameters['nstars'] *= 2;
	$options = array (
			'disabled' => 'true',
			'split' => 2);

	// Get current value
	$curvals = db_single ('SELECT AVG(rating) AS avgrating,
			COUNT(rating) AS nvotes FROM ui_rating
			WHERE `group`=? AND item=? GROUP BY item',
			$parameters['group'], $parameters['item']);
	$value = round ($curvals->avgrating * 2);
}
else {
	// Add AJAX scripts
	page_add_script (site_prefix () . '/js/rpc-compressed.js');
	page_add_script (site_prefix () . '/inc/app/ui/js/rpc.rating.js');

	$options = array (
			'callback' => 'function(ui, type, value){
			if (type == "star") {' . 
			"rating.set('{$parameters['group']}', '{$parameters['item']}', '{$username}', value);" .
			'}
			else {' .
			"rating.unset('{$parameters['group']}', '{$parameters['item']}', '{$username}');" .
			'}}');

	// Get current value
	$value = db_shift ('SELECT rating FROM ui_rating
			WHERE `group`=? AND item=? and user=?',
			$parameters['group'], $parameters['item'], $username);
}

$caption = true;
switch ($parameters['nstars'] == 5) {
	case 5:
		$values = array (intl_get ('Cancel'),
			intl_get ('Poor'),
			intl_get ('Nothing special'),
			intl_get ('Okay'),
			intl_get ('Pretty cool'),
			intl_get ('Awesome!'));
		break;
	default:
		$caption = false;
		$values = range (0, $parameters['nstars'], 1);
}


// Create Widget
$name = $parameters['group'] . '-stars';
$stars = new MF_Widget_rating ($name);
$stars->setValues ($values);
$stars->setValue ($value);
$stars->starOptions = $options;
$stars->caption = $caption;

if (! session_valid () && $parameters['anon'] == 'no') {
	$stars->append = '<a href="' . site_prefix () . '/sitemember-login-action">' . intl_get ('Sign in to rate.') . '</a>';
}
else if ($parameters['readonly'] == 'yes') {
	switch ($curvals->nvotes) {
		case 0:
			$stars->append = intl_get ('No rating.');
			break;
		case 1:
			$stars->append = intl_get ('1 rating.');
			break;
		default:
			$stars->append = intl_get ('{nvotes} ratings.', $curvals);
			break;
	}
}


echo $stars->display (false);



?>
