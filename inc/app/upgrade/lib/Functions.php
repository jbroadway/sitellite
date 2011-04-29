<?php

function upgrade_version_num () {
	return array_shift (explode ('-', SITELLITE_VERSION));
}

function upgrade_box () {
	return str_replace ('.', '_', upgrade_version_num ());
}

function upgrade_db ($out = false) {
	loader_import ('saf.File.Directory');
	$files = Dir::find ('*-' . upgrade_version_num () . '.sql', 'upgrade');
	if (count ($files) == 0) {
		return true;
	}
	$file = array_shift ($files);

	$sql = join ('', file ($file));
	$sql = sql_split ($sql);

	foreach ($sql as $query) {
		if ($out) {
			echo '<pre>' . $query . '</pre>';
		}
		if (! db_execute ($query)) {
			return false;
		}
	}
	return true;
}

function upgrade_ran ($v = false) {
	if (! $v) {
		$v = upgrade_version_num ();
	}
	return db_shift ('select count(*) from sitellite_upgrade where num = ?', $v);
}

function upgrade_log ($v = false) {
	if (! $v) {
		$v = upgrade_version_num ();
	}
	return db_execute (
		'insert into sitellite_upgrade values (?, ?, now())',
		$v,
		session_username ()
	);
}

function upgrade_exists ($v = false) {
	if (! $v) {
		$v = upgrade_version_num ();
	}
	$folder = upgrade_box ($v);
	return @file_exists ('inc/app/upgrade/boxes/' . $folder);
}

?>