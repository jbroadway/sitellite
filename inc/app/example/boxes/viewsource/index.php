<?php

/*
 * View Source Example
 *
 * This example simply renders the source for a given example, so that we may
 * inspect them from within the browser.
 */

// input validation
if (empty ($parameters['example'])) {
	// default to viewing self
	$parameters['example'] = 'viewsource';
} elseif (preg_match ('/[^a-zA-Z0-9_-]/', $parameters['example'])) {
	// check for illegal characters in example name
	return '';
}

if (! in_array ($parameters['type'], array ('boxes', 'forms', 'html', 'settings'))) {
	// if type is not in list, assume default of 'boxes' (for viewing self)
	$parameters['type'] = 'boxes';
}

// file extensions for each type
$extensions = array (
	'boxes' => '/index.php',
	'forms' => '/index.php',
	'html' => '.spt',
	'settings' => '/settings.php',
);

// generate the highlighted source via highlight_file()
ob_start ();
if ($parameters['type'] != 'settings') {
	highlight_file ('inc/app/example/' . $parameters['type'] . '/' . $parameters['example'] . $extensions[$parameters['type']]);
} else {
	highlight_file ('inc/app/example/forms/' . $parameters['example'] . $extensions[$parameters['type']]);
}
$parameters['source'] = ob_get_contents ();
ob_end_clean ();

// display the output
page_title (intl_get ('Viewing Source of Example') . ': ' . $parameters['example']);

echo template_simple ('viewsource.spt', $parameters);

?>