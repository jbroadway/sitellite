<?php

if (! session_allowed ('imagechooser_delete', 'rw', 'resource')) {
	die ('Delete not permitted.');
}

$res = db_shift_array ('select id from sitellite_page where body like ?', '%' . $parameters['location'] . '/' . $parameters['src'] . '%');
if (count ($res) > 0) {
	$parameters['deleted'] = false;
	page_title (intl_get ('Image in Use') . ': ' . $parameters['location'] . '/' . $parameters['src']);
	$parameters['err'] = intl_get ('Unable to delete image because it is still in use on the following pages:');
	$parameters['list'] = $res;
} else {
	if (! @unlink (site_docroot () . $parameters['location'] . '/' . $parameters['src'])) {
		$parameters['deleted'] = false;
		page_title (intl_get ('Delete Failed') . ': ' . $parameters['location'] . '/' . $parameters['src']);
		$parameters['err'] = intl_get ('Unable to delete image.  Check your server filesystem permissions and try again.');
	} else {
		$parameters['deleted'] = true;
		page_title (intl_get ('Image Deleted') . ': ' . $parameters['location'] . '/' . $parameters['src']);
	}
}

if ($parameters['admin']) {
	$app = '-admin-action';
} else {
	$app = '-app';
}

global $cgi;

if ($parameters['err']) {
	session_set ('imagechooser_err', $parameters['err']);
	session_set ('imagechooser_pagelist', $parameters['list']);
} else {
	session_set ('sitellite_alert', intl_get ('The image has been deleted.'));
}

header ('Location: ' . site_prefix () . '/index/imagechooser' . $app . '?name=' . $cgi->name . '&format=' . urlencode ($cgi->format) . '&location=' . urlencode ($cgi->location) . '&attrs=' . urlencode ($cgi->attrs));

//echo template_simple ('delete.spt', $parameters);

exit;

?>