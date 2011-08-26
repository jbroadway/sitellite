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
page_title (intl_get ('Creating folder in') . ': ' . $data['location']);

if (! empty ($cgi->folder)) {
	if (@file_exists ($prefix . '/' . $cgi->folder)) {
		$data['err'] = intl_get ('The folder name you have chosen already exists.  Please choose another.');
	} elseif (preg_match ('/\s/',$cgi->folder)) {
		$data['err'] = intl_get ('Your folder name contains whitespace.');
	} elseif (preg_match ('/^[^a-zA-Z0-9\. _-]+$/', $cgi->folder)) {
		$data['err'] = intl_get ('Your folder name contains invalid characters.  Allowed characters are') . ': A-Z 0-9 . _ -.';
	} else {
		if (! mkdir ($prefix . '/' . $cgi->folder)) {
			$data['err'] = intl_get ('Unknown error attempting to create folder.');
		} else {
			umask (0000);
			chmod ($prefix . '/' . $cgi->folder, 0755);
			if ($cgi->admin) {
				$app = '-admin-action';
			} else {
				$app = '-app';
			}
			header ('Location: ' . site_prefix () . '/index/imagechooser' . $app . '?name=' . $cgi->name . '&format=' . urlencode ($cgi->format) . '&location=' . urlencode ($cgi->location . '/' . $cgi->folder) . '&attrs=' . urlencode ($cgi->attrs));
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
echo template_simple ('folder.spt', $data);

exit;

?>