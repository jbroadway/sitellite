<?php

/*
 * List Example
 *
 * This example simply lists all of the boxes and forms in the example app.
 */

loader_import ('saf.File.Directory');

// get list of boxes
$d = new Dir ('inc/app/example/boxes');
$boxes = array ();

foreach ($d->readAll () as $file) {
	if (strpos ($file, '.') === 0 || ! @is_dir ('inc/app/example/boxes/' . $file) || $file == 'CVS') {
		continue;
	}
	$box = array ('file' => $file);
	if (@file_exists ('inc/app/example/html/' . $file . '.spt')) {
		$box['template'] = true;
	}
	if (@file_exists ('inc/app/example/boxes/' . $file . '/settings.php')) {
		$settings = @ini_parse ('inc/app/example/boxes/' . $file . '/settings.php');
		$box['name'] = $settings['Meta']['name'];
	} else {
		$box['name'] = $file;
	}
	$boxes[] = $box;
}

// get list of forms
$d = new Dir ('inc/app/example/forms');
$forms = array ();

foreach ($d->read_all () as $file) {
	if (strpos ($file, '.') === 0 || ! @is_dir ('inc/app/example/forms/' . $file) || $file == 'CVS') {
		continue;
	}
	$form = array ('file' => $file);
	if (@file_exists ('inc/app/example/html/' . $file . '.spt')) {
		$form['template'] = true;
	}
	if (@file_exists ('inc/app/example/forms/' . $file . '/settings.php')) {
		$form['settings'] = true;
	}
	$form['name'] = ucwords (str_replace ('_', ' ', $file));
	$forms[] = $form;
}

// display the example list
echo template_simple (
	'list.spt',
	array (
		'boxes' => $boxes,
		'forms' => $forms,
	)
);

?>