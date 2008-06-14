<?php

/**
 * @package AppDoc
 *
 */

/**
 * Walks through the boxes or forms folders and recursively builds a $list of found actions.
 *
 * @param string
 * @param string
 * @param string
 * @param array
 * @return boolean
 *
 */
function help_walker ($appname, $type = 'boxes', $directory, &$list) {
	$dir = new Dir ($directory);
	if (! $dir->handle) {
		return false;
	}
	foreach ($dir->read_all () as $file) {
		if (strpos ($file, '.') === 0 || $file == 'CVS') {
			continue;
		} elseif (@is_dir ($directory . '/' . $file)) {

			$name = str_replace (getcwd () . '/inc/app/' . $appname . '/' . $type . '/', '', $directory . '/' . $file);

			$box = array (
				'name' => $name,
				'alt' => ucfirst (basename ($name)),
				$type => array (),
				'is_dir' => false,
			);

			if (! @file_exists ($directory . '/' . $file . '/index.php')) {
				$box['is_dir'] = true;
			} else {
				if ($type == 'boxes' && @file_exists ($directory . '/' . $file . '/settings.php')) {
					$settings = ini_parse ($directory . '/' . $file . '/settings.php');
					$box['alt'] = $settings['Meta']['name'];
				}
			}

			$list[$name] = $box;
			help_walker ($appname, $type, $directory . '/' . $file, $list[$name][$type]);
		}
	}
	return true;
}

/**
 * Looks recursively through the directory structure for an access.php file.
 * If found, it will return this file parsed into an array.
 *
 * @param string
 * @param string
 * @return array
 *
 */
function help_get_access ($path, $stop) {
	if (@file_exists ($path . '/access.php')) {
		return ini_parse ($path . '/access.php', false);
	}
	$info = pathinfo ($path);
	$newpath = $info['dirname'];
	if ($newpath == $stop) {
		return array ();
	}
	return help_get_access ($newpath, $stop);
}

/**
 * Returns the strings 'on' or 'off' when a value is a boolean ('1' or '0'), or
 * else returns the value itself.
 *
 * @param string
 * @return string
 *
 */
function help_typecheck ($value) {
	if ($value === '1') {
		return 'on';
	} elseif ($value === '0') {
		return 'off';
	}
	return $value;
}

/**
 * Returns a pseudo-datatype based on the given type value, which is a MailForm widget name.
 *
 * @param string
 * @return string
 *
 */
function help_typechange ($value) {
	switch ($value) {
		case 'date':
		case 'calendar':
			return 'date';
		case 'datetime':
		case 'datetimeinterval':
			return 'date/time';
		case 'timestamp':
			return 'timestamp';
		case 'time':
		case 'timeinterval':
			return 'time';
		default:
			return 'string';
	}
}

?>