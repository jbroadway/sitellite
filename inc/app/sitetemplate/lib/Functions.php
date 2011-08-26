<?php

loader_import ('saf.File.Directory');

function sitetemplate_get_apps () {
	$dir = new Dir ('inc/app');
	$apps = array ();

	foreach ($dir->readAll () as $file) {
		if ($file != 'CVS' && strpos ($file, '.') !== 0) {
			if (@file_exists ('inc/app/' . $file . '/conf/config.ini.php')) {
				$apps[] = $file;
			}
		}
	}

	return $apps;
}

function sitetemplate_get_boxes ($app) {
	$boxes = Dir::getStruct ('inc/app/' . $app . '/boxes');

	foreach ($boxes as $k => $b) {
		if ($b == 'CVS' || ! @file_exists ($b . '/index.php') || strpos ($b, '.') === 0) {
			unset ($boxes[$k]);
		}
	}

	foreach ($boxes as $k => $b) {
		$boxes[$k] = str_replace ('inc/app/'. $app . '/boxes/', '', $b);
	}

	return $boxes;
}

?>