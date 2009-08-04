<?php

// Rotate the log file of the scheduler



// BEGIN CLI KEEPOUT CHECKING
if (php_sapi_name () !== 'cli') {
    // Add these lines to the very top of any file you don't want people to
    // be able to access directly.
    header ('HTTP/1.1 404 Not Found');
    echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
        . "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
        . "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
        . $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
    exit;
}
// END CLI KEEPOUT CHECKING

$logfile = site_docroot () . '/inc/app/scheduler/log/scheduler.log';
$info = stat ($logfile);
$gz = function_exists ('gzencode')?'.gz':'';

// Rotate each week, or when filesize > 1MB)
if (($info['ctime'] < time () - 604800) || ($info['size'] > 1048576)) {

	for ($i=8; $i>0; --$i) {
		if (file_exists ($logfile.'.'.$i.$gz)) {
			rename ($logfile.'.'.$i.$gz, $logfile.'.'.($i+1).$gz);
		}
	}

	if (file_exists ($logfile.'.0')) {
		if (!empty ($gz)) {
			$data = implode("", file($logfile.'.0'));
			$gzdata = gzencode($data);
			$fp = fopen($logfile.'.1.gz', "w");
			fwrite($fp, $gzdata);
			fclose($fp);
		}
		else {
			rename ($logfile.'.0', $logfile.'.1');
		}
	}

	rename ($logfile, $logfile.'.0');
	touch ($logfile);
}

?>
