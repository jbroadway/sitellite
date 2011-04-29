<?php

global $cgi;

$path = 'inc/lang/';

$d = dir ($path);
while (false !== ($file = $d->read())) {
	if (substr ($file, 2) == '.dates.php') {
		$info = ini_parse ($path.$file);

		foreach ($cgi->_key as $k) {
			unset ($info['formats'][$k]);
		}

		$fp = fopen ($path.$file, 'w');
		if (! $fp) {
			page_title ('An Error Occurred');
			echo 'Error: Failed to open ';
			echo $file;
			echo ' file!';
			return;
		}

		fwrite ($fp, ini_write ($info));
		fclose ($fp);
	}
}

$d->close();

header ('Location: ' . site_prefix () . '/index/multilingual-dates-edit-action?lang=' . $cgi->lang . 
	'&charset=' . $cgi->charset . '&default=' . $cgi->default);
exit;

?>
