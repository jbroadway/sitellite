<?php

if (! isset ($parameters['orig'])) {
	list ($filename, $remote) = explode ('?snipshot_output=', $parameters['file']);

	list ($tmp, $parameters['orig']) = explode ('/pix/', $filename);
	$parameters['orig'] = 'pix/' . $parameters['orig'];
	$parameters['remote'] = 'cache/_' . str_replace ('/', '_', $parameters['orig']);

	loader_import ('saf.File');

	$filedata = @join ('', @file ($remote));

	file_overwrite ($parameters['remote'], $filedata);

	page_title (intl_get ('Saving File') . ': ' . $filename);

	$max_width = 350;
	$max_height = 400;

	list ($w, $h) = getimagesize ($parameters['orig']);
	if ($max_height < $h || $max_width < $w) {
		if ($h > $w) {
			$w = $w * ($max_height / $h);
			$h = $max_height;
		} else {
			$h = $h * ($max_width / $w);
			$w = $max_width;
		}
	}
	$parameters['orig_w'] = $w;
	$parameters['orig_h'] = $h;

	$parameters['dir'] = dirname ($parameters['orig']);

	list ($w, $h) = getimagesize ($parameters['remote']);
	if ($max_height < $h || $max_width < $w) {
		if ($h > $w) {
			$w = $w * ($max_height / $h);
			$h = $max_height;
		} else {
			$h = $h * ($max_width / $w);
			$w = $max_width;
		}
	}
	$parameters['remote_w'] = $w;
	$parameters['remote_h'] = $h;

	echo template_simple ('save.spt', $parameters);
} else {
	$save_as = $parameters['orig'];
	if (! empty ($parameters['new'])) {
		$save_as = dirname ($parameters['orig']) . '/' . $parameters['new'];
	}

	rename ($parameters['remote'], $save_as);

	header ('Location: ' . site_prefix () . '/index/imagechooser-admin-action?location=/' . dirname ($parameters['orig']));
	exit;
}

?>