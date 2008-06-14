<?php

// settings stuff
global $cgi;

if (empty ($cgi->format)) {
	$cgi->format = 'html';
}

// get the path root from the imagechooser-path session variable,
// and if not then default to /pix.
$path = session_get ('imagechooser_path');
if (! $path) {
	$path = '/pix';
}

$data = array (
	'location' => false,
	'src' => $cgi->src,
	'onload' => '',
	'name' => $cgi->name,
);

if (
		empty ($cgi->location) ||
		strpos ($cgi->location, $path . '/') !== 0 ||
		strstr ($cgi->location, '..') ||
		! @is_dir (site_docroot () . $cgi->location)
) {
	$data['location'] = $path;
	if (strpos ($data['src'], $data['location']) === 0) {
		$data['src'] = substr ($data['src'], strlen ($data['location'] . '/'));
	}
} else {
	$data['location'] = $cgi->location;
	$up = preg_replace ('|/[^/]+$|', '', $data['location']);
	if (! empty ($up)) {
		$data['up'] = $up;
	}
}

// get all the data
page_title (intl_get ('Image') . ': ' . $data['src']);

list ($data['width'], $data['height']) = getimagesize (site_docroot () . $data['location'] . '/' . $data['src']);

if ($cgi->attrs == 'false') {
	$data['onload'] = 'imagechooser_' . $cgi->name . '_select (\'' . $data['location'] . '/' . $data['src'] . '\')';
}

// show me the money
echo template_simple ('select.spt', $data);

exit;

?>