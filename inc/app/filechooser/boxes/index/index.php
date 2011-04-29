<?php

// keep non-admins out
/*
if (! session_admin ()) {
	echo '<script language="javascript"><!--
		window.close ();
	// --></script>';
	exit;
}
*/

// settings stuff
global $cgi;
$root = '/inc/data';

if (empty ($cgi->format)) {
	$cgi->format = 'html';
}

// get the path root from the filechooser-path session variable,
// and if not then default to /inc/data.

$data = array (
	'location' => false,
	'up' => false,
	'add' => false,
	'subfolders' => array (),
	'files' => array (),
	'slash' => '',
	'url_prefix' => '/index/cms-filesystem-action/',
	'base_nice_url' => '',
	'friendly_path' => '',
);

$path = session_get ('filechooser_path');
if (! $path) {
	$path = $root;
	$data['base_nice_url'] = "Web Files";
} else { 
	$data['base_nice_url'] = $path; }


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
page_title (intl_get ('Folder') . ': ' . $data['location']);

$dir = site_docroot () . $data['location'];
if ($dh = opendir ($dir)) {
	while (false !== ($file = readdir ($dh))) {
		if (strpos ($file, '.') === 0 || $file == 'CVS') {
			continue;
		}
		if (@is_dir ($dir . '/' . $file)) {
			$data['subfolders'][] = $file;
		} else { //it must be a file, include it in data's files
			$data['files'][] = array ('name' => $file );
		}
	}
}

function filechooser_sort ($a, $b) {
	if ($a['name'] == $b['name']) {
		return 0;
	}
	return (strtolower ($a['name']) < strtolower ($b['name'])) ? -1 : 1;
}

sort ($data['subfolders']);
usort ($data['files'], 'filechooser_sort');

if(strlen($data['location'])!=(strlen($root)))
{
	$data['slash']='/';
} else { $data['slash']=''; }

$data['friendly_path'] = substr ($data['location'], strlen ($root) + 1);
$data['folder_path'] = substr ($data['location'], strlen ($root) + 1);
if (! empty ($data['folder_path'])) {
	$data['folder_path'] .= '/';
}
$data['url_prefix'] = $data['url_prefix'] . $data['friendly_path'];
// show me the money
//info($data);

// determine whether they can add files
loader_import ('cms.Versioning.Rex');
$rex = new Rex ('sitellite_filesystem');
if (isset ($rex->info['Collection']['add']) && $rex->info['Collection']['add'] == false) {
	// no add
} elseif (session_allowed ('add', 'rw', 'resource')) {
	$data['add'] = true;
}

template_simple_register ('cgi', $cgi);
echo template_simple ('index.spt', $data);

exit;

?>