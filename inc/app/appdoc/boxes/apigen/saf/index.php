<?php

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

$app = 'saf';
$read = 'saf/lib';
$write = 'saf/docs/api';
$docs = 'saf/docs';
$fullname = 'SAF';

if (! @is_dir ($read)) {
	die ('No lib folder found.');
}

if (! @is_dir ($docs)) {
	$r = mkdir ($docs, 0777);
	if (! $r) {
		die ('No docs folder found.  Attempt to create failed.');
	}
}

if (! @is_dir ($write)) {
	$r = mkdir ($write, 0777);
	if (! $r) {
		die ('No docs/api folder found.  Attempt to create failed.');
	}
}

if (! @is_writeable ($write)) {
	die ('Cannot write to docs/api folder.  Please change your filesystem permissions.');
}

function appdoc_apigen_error_handler ($errno, $errstr, $errfile, $errline) {
	switch ($errno) {
		case FATAL:
			echo "FATAL [$errno] $errstr\n$errfile ($errline)\n";
			exit (1);
			break;
		case ERROR:
			echo "ERROR [$errno] $errstr\n$errfile ($errline)\n";
			break;
		case WARNING:
			echo "WARNING [$errno] $errstr\n$errfile ($errline)\n";
			break;
		default:
			echo "UNKNOWN [$errno] $errstr\n$errfile ($errline)\n";
			break;
	}
}
set_error_handler ('appdoc_apigen_error_handler');

while (ob_get_level ()) {
	ob_end_clean ();
}
ob_implicit_flush (true);

set_time_limit (0);
echo "Generating API documentation, please be patient.\n\n";

passthru ('./saf/lib/PEAR/PhpDocumentor/phpdoc -d ' . $read . ' -s -t ' . $write . ' -ti "' . $fullname . '" -o HTML:Smarty:SitelliteDotOrg -dn ' . $app . ' -i PEAR/,Ext/');

echo "Finished.";
set_time_limit (30);

exit;

?>
