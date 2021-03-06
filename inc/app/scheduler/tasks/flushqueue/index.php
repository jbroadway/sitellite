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

loader_import ('cms.Workspace.Notice');

$n = new WorkspaceNotice ();

if (! $n->flushQueue ('email')) {
	if (! empty ($n->error)) {
		echo $n->error . "\n";
	}
}

if (! $n->flushQueue ('jabber')) {
	if (! empty ($n->error)) {
		echo $n->error . "\n";
	}
}

if (! $n->flushQueue ('sms')) {
	if (! empty ($n->error)) {
		echo $n->error . "\n";
	}
}

?>