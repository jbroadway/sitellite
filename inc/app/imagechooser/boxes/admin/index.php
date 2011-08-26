<?php

if (! session_admin ()) {
	page_title ( 'Image Manager - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/imagechooser-admin-action">
		<table cellpadding="5" border="0">
			<tr>
				<td>Username</td>
				<td><input type="text" name="username" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" name="password" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Enter" /></td>
			</tr>
		</table>
		</form>'
	);

	return;
}

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

// get all the data
page_title (intl_get ('Folder') . ': ' . $data['location']);

if (strpos ($data['location'], '/') !== 0) {
	$data['location'] = '/' . $data['location'];
}

$dir = site_docroot () . $data['location'];

if (! @is_dir ($dir)) {
	echo '<p>Error: Image folder doesn\'t exist</p>';
	echo '<p>Path: ' . $dir . '</p>';
	exit;
}
if ($dh = opendir ($dir)) {
	while (false !== ($file = readdir ($dh))) {
		if (strpos ($file, '.') === 0 || $file == 'CVS') {
			continue;
		}
		if (@is_dir ($dir . '/' . $file)) {
			$data['subfolders'][] = $file;
		} elseif (preg_match ('/\.(jpg|gif|png)$/i', $file)) {
			list ($w, $h) = getimagesize (site_docroot () . $data['location'] . '/' . $file);
			$data['images'][] = array ('name' => $file, 'width' => $w, 'height' => $h);
		}
	}
}

function image_sort ($a, $b) {
	if ($a['name'] == $b['name']) {
		return 0;
	}
	return (strtolower ($a['name']) < strtolower ($b['name'])) ? -1 : 1;
}

sort ($data['subfolders']);
usort ($data['images'], 'image_sort');

if (! is_writeable (site_docroot () . $data['location'])) {
	$data['writeable'] = false;
} else {
	$data['writeable'] = true;
}

if (! session_allowed ('imagechooser_delete', 'rw', 'resource')) {
	$data['delete'] = false;
} else {
	$data['delete'] = true;
}

if (session_get ('imagechooser_err')) {
	$data['err'] = session_get ('imagechooser_err');
	session_set ('imagechooser_err', null);
}

if (session_get ('imagechooser_pagelist')) {
	$data['pagelist'] = session_get ('imagechooser_pagelist');
	session_set ('imagechooser_pagelist', null);
}

page_add_script (site_prefix () . '/js/dialog.js');
page_add_script (template_simple ('js.spt'));

function output_options ($name) {
	$info = pathinfo ($name);
	return '{&quot;filetype&quot;: &quot;' . strtolower ($info['extension']) . '&quot;}';
}

// show me the money
template_simple_register ('cgi', $cgi);
echo template_simple ('admin.spt', $data);

?>