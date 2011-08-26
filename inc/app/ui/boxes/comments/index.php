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
	$parameters['anon'] = 'yes';
}
if (!isset ($parameters['readonly'])) {
	$parameters['readonly'] = 'no';
}

$comments = new Comments ($parameters);
$comments->get ();
if (session_valid () || $parameters['anon'] == 'yes') {
	$comments->createAddForm ($username);
}

//page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');
page_add_script (site_prefix () . '/js/rpc-compressed.js');
page_add_script (template_simple ('comment-add.js', $comments));
page_add_style (site_prefix () . '/inc/app/ui/html/comments.css');

echo template_simple ('comments.spt', $comments);

?>
