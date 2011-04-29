<?php

/**
 * Creates a cache file name/path based on the type, package and class.
 */
function docs_cache_name ($type, $pkg = false, $cls = false) {
	$path = 'inc/app/docs/data/' . $type;
	if ($pkg) {
		$path .= '_' . $pkg;
	}
	if ($cls) {
		$path .= '_' . $cls;
	}
	$path .= '.php';
	return $path;
}

/**
 * Tries to retrieve the cached data.  You can test if it returned
 * successfully by testing the return value's type (e.g. using is_array()
 * or the like).
 */
function docs_cache ($type, $pkg = false, $cls = false) {
	$path = docs_cache_name ($type, $pkg, $cls);
	if (! @file_exists ($path)) {
		return false;
	}
	return unserialize (file_get_contents ($path));
}

/**
 * Send the data and the info to generate the name for later retrieval
 * and this will store the data for docs_cache() and return the same
 * data, so you can simply say:
 * $data = docs_cache_store (get_data (), 'type', 'package', 'class');
 */
function docs_cache_store ($data, $type, $pkg = false, $cls = false) {
	$path = docs_cache_name ($type, $pkg, $cls);
	@file_put_contents ($path, serialize ($data));
	return $data;
}

?>