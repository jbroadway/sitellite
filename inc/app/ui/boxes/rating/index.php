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

$readonly = $parameters['readonly'];
if ( ! session_valid ()) {
	if ($parameters['anon'] == 'yes') {
		$username = $_SERVER["REMOTE_ADDR"];
	}
	else {
		// if user not logged and no anonymous rating,
		// show average rating instead
		$readonly = 'yes';
		$username = null;
	}
}
else {
	$username = session_username ();
}

if ($username) {
	// Get current value
	$value = db_shift ('SELECT rating FROM ui_rating
			WHERE `group`=? AND item=? and user=?',
			$parameters['group'], $parameters['item'], $username);
	if ($value) {
		// Already voted!
		$readonly = 'yes';
	}
}
$curvals = db_single ('SELECT AVG(rating) AS avgrating,
		COUNT(rating) AS nvotes FROM ui_rating
		WHERE `group`=? AND item=? GROUP BY item',
		$parameters['group'], $parameters['item']);

if ($readonly == 'yes') {
	$parameters['nstars'] *= 2;
	$options = array (
			'disabled' => 'true',
			'split' => 2);

	// Get current value
	$value = round ($curvals->avgrating * 2);
}
else {
	// Add AJAX scripts
	page_add_script (site_prefix () . '/js/rpc-compressed.js');
	page_add_script (site_prefix () . '/inc/app/ui/js/rpc.rating.js');

	$options = array (
			'oneVoteOnly' => 'true',
			'callback' => 'function(ui, type, value){
			if (type == "star") {' . 
			"rating.set('{$parameters['group']}', '{$parameters['item']}', '{$username}', value);" .
			'}
			else {' .
			"rating.unset('{$parameters['group']}', '{$parameters['item']}', '{$username}');" .
			'}}');
	$value = round ($curvals->avgrating);
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

if ($parameters['readonly'] == 'yes') {
	$stars->append = '';
} elseif (! session_valid () && $parameters['anon'] == 'no') {
	$stars->append = '<a href="' . site_prefix () . '/sitemember-login-action">' . intl_get ('Sign in to rate.') . '</a>';
} else {
	switch ($curvals->nvotes) {
		case 0:
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

if (strpos ($stars->append, 'rating')) {
	page_add_script ('$(document).ready( function () {
		$("#' . $parameters['group'] . '-stars-wrapper a").attr("onmouseover", "captionOff (\'' . $parameters['group'] . '\')");
		$("#' . $parameters['group'] . '-stars-wrapper a").attr("onmouseout", "captionOn (\'' . $parameters['group'] . '\', \'' . $stars->append . '\')");
	$("#' . $parameters['group'] . '-stars-ratings-text").html("' . $stars->append . '").show();
	$("#' . $parameters['group'] . '-stars-caption").hide ();
});
');
}
?>
