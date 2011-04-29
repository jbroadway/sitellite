<?php

exit;

global $cgi;

if ($cgi->appname == 'GLOBAL') {
	$file = 'inc/lang/languages.php';
	$path = 'inc/lang';
} else {
	$file = 'inc/app/' . $cgi->appname . '/lang/languages.php';
	$path = 'inc/app/' . $cgi->appname . '/lang';
}

$info = ini_parse ($file);

foreach ($cgi->_key as $k) {
	unset ($info[$k]);
}

$fp = fopen ($file, 'w');
if (! $fp) {
	page_title ('An Error Occurred');
	echo 'Error: Failed to open languages.php file!';
	return;
}

fwrite ($fp, ini_write ($info));
fclose ($fp);

foreach ($cgi->_key as $k) {
	if (@file_exists ($path . '/' . $k . '.php')) {
		unlink ($path . '/' . $k . '.php');
	}
}

header ('Location: ' . site_prefix () . '/index/appdoc-translation-action?appname=' . $cgi->appname);
exit;

?>