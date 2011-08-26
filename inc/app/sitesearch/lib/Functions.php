<?php

function chmod_recursive ($path, $mode) {
	umask (0000);

	if (! is_dir ($path)) {
		return chmod ($path, $mode);
	}

	$dh = opendir ($path);
	while ($file = readdir ($dh)) {
		if ($file != '.' && $file != '..') {
			$fullpath = $path . '/' . $file;
			if (! is_dir ($fullpath)) {
				if (! chmod ($fullpath, $mode)) {
					return false;
				} else {
					if (! chmod_recursive ($fullpath, $mode)) {
						return false;
					}
				}
			}
		}
	}
	closedir ($dh);
	if (chmod ($path, $mode)) {
		return true;
	}
	return false;
}

?>