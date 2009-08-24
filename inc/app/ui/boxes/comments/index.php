<?php

loader_import ('ui.Comments');

if (!isset ($parameters['group'])) {
	$parameters['group'] = 'sitellite_page';
}
if (!isset ($parameters['item'])) {
	$parameters['item'] = $GLOBALS['page']->id;
}
if (!isset ($parameters['approve'])) {
	$parameters['approve'] = 'yes';
}
if (!isset ($parameters['anon'])) {
	$parameters['anon'] = 'no';
}
if (!isset ($parameters['readonly'])) {
	$parameters['readonly'] = 'no';
}

if ( ! session_valid ()) {
	if ($parameters['anon'] == 'yes') {
		$username = $_SERVER["REMOTE_ADDR"];
	}
	else {
		// if user not logged and no anonymous comments,
		// only show actual comments
		$parameters['readonly'] = 'yes';
		$username = null;
	}
}
else {
	$username = session_username ();
}

$comments = new Comments ($parameters);
$comments->get ();
if ($username) {
	$comments->createAddForm ($username);
}

page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');
page_add_script (site_prefix () . '/js/rpc-compressed.js');
page_add_script (template_simple ('comment-add.js', $comments));
page_add_style (site_prefix () . '/inc/app/ui/html/comments.css');

echo template_simple ('comments.spt', $comments);

?>
