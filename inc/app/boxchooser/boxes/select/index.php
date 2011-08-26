<?php

// settings stuff
global $cgi;

if (empty ($cgi->format)) {
	$cgi->format = 'html';
}

// get the path root from the imagechooser-path session variable,
// and if not then default to /pix.
$path = session_get ('filechooser_path');
if (! $path) {
	$path = '/inc/data';
}

$data = array (
	'location' => false,
	'src' => $cgi->src,
	'onload' => '',
);

if (
		empty ($cgi->location) ||
		strpos ($cgi->location, $path . '/') !== 0 ||
		strstr ($cgi->location, '..') ||
		! @is_dir (site_docroot () . $cgi->location)
) {
	$data['location'] = $path;
} else {
	$data['location'] = $cgi->location;
	$up = preg_replace ('|/[^/]+$|', '', $data['location']);
	if (! empty ($up)) {
		$data['up'] = $up;
	}
}

// get all the data
page_title (intl_get ('Image') . ': ' . $data['src']);

//list ($data['width'], $data['height']) = getimagesize (site_docroot () . $data['location'] . '/' . $data['src']);

if ($cgi->attrs == 'false') {
	$data['onload'] = 'filechooser_select (\'' . $data['location'] . '/' . $data['src'] . '\')';
}

// show me the money
echo template_simple ('select.spt', $data);

exit;

?>