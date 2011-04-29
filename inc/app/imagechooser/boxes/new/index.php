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
	'up' => false,
	'err' => false,
	'subfolders' => array (),
	'images' => array (),
	'name' => $cgi->name,
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

$prefix = substr ($cgi->location, 1);

// get all the data
page_title (intl_get ('Adding image in') . ': ' . $data['location']);

if (empty ($cgi->filename)) {
	$filename = $cgi->file->name;
} else {
	$filename = $cgi->filename;
}

if ($cgi->file) {
	if (@file_exists ($prefix . '/' . $filename)) {
		$data['err'] = intl_get ('The file name you have chosen already exists.  Please choose another.');
	} elseif (preg_match ('/^[^a-zA-Z0-9\. _-]+$/', $filename)) {
		$data['err'] = intl_get ('Your file name contains invalid characters.  Allowed characters are') . ': A-Z 0-9 . _ - and space.';
	} elseif (! preg_match ('/\.(jpg|gif|png)$/i', $filename)) {
		$data['err'] = intl_get ('Your image file must be in one of the following formats') . ': JPG, GIF or PNG';
	} else {
		if (! $cgi->file->move ($prefix, $filename)) {
			$data['err'] = intl_get ('Unknown error attempting to create file.');
		} else {
			umask (0000);
			chmod ($prefix . '/' . $filename, 0755);
			if ($cgi->admin) {
				$app = '-admin-action';
			} else {
				$app = '-app';
			}
			header ('Location: ' . site_prefix () . '/index/imagechooser' . $app . '?name=' . $cgi->name . '&format=' . urlencode ($cgi->format) . '&location=' . urlencode ($cgi->location) . '&attrs=' . urlencode ($cgi->attrs));
			exit;
		}
	}
}

if ($cgi->admin) {
	$data['app'] = '-admin-action';
} else {
	$data['app'] = '-app';
}

// show me the money
echo template_simple ('new.spt', $data);

exit;

?>