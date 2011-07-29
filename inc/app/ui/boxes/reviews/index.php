<?php

loader_import ('ui.Reviews');

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
if (session_valid ()) {
	$parameters['user'] = session_username ();
}
else {
	$parameters['user'] = strtr ($_SERVER["REMOTE_ADDR"], '.', '-');
}
if (!isset ($parameters['nstars'])) {
        $parameters['nstars'] = 5;
}

$reviews = new Reviews ($parameters);
$reviews->get ();

if (session_valid () || $parameters['anon'] == 'yes') {
	$reviews->createAddForm ();
}

//page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');
page_add_script (site_prefix () . '/js/rpc-compressed.js');
page_add_script (template_simple ('review-add.js', $reviews));
page_add_style (site_prefix () . '/inc/app/ui/html/comments.css');

echo template_simple ('comments.spt', $reviews);

?>
