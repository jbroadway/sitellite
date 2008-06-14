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
	'up' => false,
	'err' => false,
	'subfolders' => array (),
	'images' => array (),
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
page_title (intl_get ('Creating folder in') . ': ' . $data['location']);

if (! empty ($cgi->folder)) {
	if (@file_exists (site_docroot () . $cgi->location . '/' . $cgi->folder)) {
		$data['err'] = intl_get ('The folder name you have chosen already exists.  Please choose another.');
	} elseif (preg_match ('/^[^a-zA-Z0-9\. _-]+$/', $cgi->folder)) {
		$data['err'] = intl_get ('Your folder name contains invalid characters.  Allowed characters are') . ': A-Z 0-9 . _ - and space.';
	} else {
		if (! mkdir (site_docroot () . $cgi->location . '/' . $cgi->folder)) {
			$data['err'] = intl_get ('Unknown error attempting to create folder.');
		} else {
			header ('Location: ' . site_prefix () . '/index/imagechooser-app?format=' . urlencode ($cgi->format) . '&location=' . urlencode ($cgi->location . '/' . $cgi->folder) . '&attrs=' . urlencode ($cgi->attrs));
			exit;
		}
	}
}

// show me the money
echo template_simple ('folder.spt', $data);

exit;

?>