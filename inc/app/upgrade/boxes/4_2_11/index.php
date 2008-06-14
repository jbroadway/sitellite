<?php

if (upgrade_ran ('4.2.11')) {
	page_title ('Upgrade 4.2.11 Already Applied');
	echo '<p><a href="' . site_prefix () . '/index/upgrade-app">Back</a></p>';
	return;
}

page_title ('Applying Upgrade 4.2.11');

echo '<p><strong>Applying database updates...</strong></p>';

// database updates
if (upgrade_db (true)) {
	echo '<p>Done.</p>';
} else {
	echo '<p>Error: ' . db_error () . '</p>';
	return;
}

echo '<p><strong>Fixing Web Files...</strong></p><p>';

set_time_limit (0);

$c = 0;

// 1. fix folder names
loader_import ('saf.File.Directory');

$struct = Dir::getStruct ('inc/data');

foreach ($struct as $dir) {
	$lower = strtolower ($dir);
	if ($dir != $lower) {
		rename ($dir, $lower);
	}
}

// 2. select all web files
$files = db_shift_array ('select distinct name from sitellite_filesystem_sv order by name asc');

foreach ($files as $file) {
	$lower = strtolower ($file);
	if ($file != $lower) {
		// 3. rename in filesystem
		rename ('inc/data/' . $file, 'inc/data/' . $lower);
	
		// 4. rename in db
		db_execute (
			'update sitellite_filesystem_sv set name = ? where name = ?',
			$lower,
			$file
		);
	
		$info = pathinfo ($file);
		$linfo = pathinfo ($lower);
		db_execute (
			'update sitellite_filesystem set name = ?, path = ?, extension = ? where name = ? and path = ? and extension = ?',
			basename ($linfo['basename'], '.' . $linfo['extension']),
			$linfo['dirname'],
			$linfo['extension'],
			basename ($info['basename'], '.' . $info['extension']),
			$info['dirname'],
			$info['extension']
		);
	
		echo $file . '<br />';
		$c++;
	}
}

echo '</p><p>Fixed ' . $c . ' files.</p>';

echo '<p><a href="' . site_prefix () . '/index/upgrade-app">Back</a></p>';

upgrade_log ();

?>