<?php

global $page;

loader_import ('devfiles.Files');
$fl = new Files;

if (DEVFILES === true || (is_string (DEVFILES) && strtotime (DEVFILES) > time ())) {
	$_files = true;
} else {
	$_files = false;
}

// delete file

if ($_files) {

if (! $fl->remove ($parameters['file'], $parameters['appname'], $parameters['id'])) {
	echo '<h1>' . intl_get ('DevFiles Error') . '</h1>';
	echo template_simple ('error.spt', $fl);
	return;
} else {
	// on success, forward back to referring page
	header ('Location: ' . $parameters['appname'] . '#files');
	exit;
}

} // end if $_files

header ('Location: ' . $parameters['appname'] . '#files');
exit;

?>