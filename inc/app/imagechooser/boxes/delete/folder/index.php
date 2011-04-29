<?php

if (! session_allowed ('imagechooser_delete', 'rw', 'resource')) {
	die ('Delete not permitted.');
}


$dir = site_docroot () . $parameters['location'] . '/' . $parameters['src'];

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
		else {
			$parameters['err'] = intl_get ('Folder not empty. Cannot delete it.');
			break;
		}
        }
}

@rmdir ($dir);


if ($parameters['admin']) {
	$app = '-admin-action';
} else {
	$app = '-app';
}

global $cgi;

if ($parameters['err']) {
	session_set ('imagechooser_err', $parameters['err']);
} else {
	session_set ('sitellite_alert', intl_get ('The folder has been deleted.'));
}

header ('Location: ' . site_prefix () . '/index/imagechooser' . $app . '?name=' . $cgi->name . '&format=' . urlencode ($cgi->format) . '&location=' . urlencode ($cgi->location) . '&attrs=' . urlencode ($cgi->attrs));

//echo template_simple ('delete.spt', $parameters);

exit;

?>
