<?php

// settings stuff
global $cgi;

if (empty ($cgi->format)) {
	$cgi->format = 'html';
}

$data = array (
	'location' => $cgi->location,
	'up' => false,
	'err' => false,
	'subfolders' => array (),
	'images' => array (),
);

// get all the data
page_title (intl_get ('Adding image in') . ': ' . $data['location']);

if (empty ($cgi->filename)) {
	$filename = $cgi->file->name;
} else {
	$filename = $cgi->filename;
}

if ($cgi->file) {
	if (@file_exists (site_docroot () . $cgi->location . '/' . $filename)) {
		$data['err'] = intl_get ('The file name you have chosen already exists.  Please choose another.');
	} elseif (preg_match ('/^[^a-zA-Z0-9\. _-]+$/', $filename)) {
		$data['err'] = intl_get ('Your file name contains invalid characters.  Allowed characters are') . ': A-Z 0-9 . _ - and space.';
	} elseif (! preg_match ('/\.(jpg|gif|png)$/i', $filename)) {
		$data['err'] = intl_get ('Your image file must be in one of the following formats') . ': JPG, GIF or PNG';
	} else {
		if (! $cgi->file->move (site_docroot () . '/inc/html/' . $cgi->location . '/pix', $filename)) {
			$data['err'] = intl_get ('Unknown error attempting to create file.');
		} else {

			umask (0000);
			chmod ('inc/html/' . $cgi->location . '/pix/' . $filename, 0777);

			header ('Location: ' . site_prefix () . '/index/sitetemplate-templateselect-action?set_name=' . $cgi->location);
			exit;
		}
	}
}

// show me the money
echo template_simple ('newimage.spt', $data);

?>