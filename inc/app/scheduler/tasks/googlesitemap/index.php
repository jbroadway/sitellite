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

// googlesitemap scheduler task
// Generates a Google Site Map of your website.
// Note: Only works in document root installations.

$urls = array ();

loader_import ('cms.Versioning.Rex');

foreach (Rex::getCollections () as $table) {
	$rex = new Rex ($table);
	if (! $rex->collection) {
		echo 'Collection "' . $table . '" failed to load.  Continuing.' . NEWLINE;
		continue;
	}

	if (empty ($rex->info['Collection']['sitesearch_url'])) {
		continue;
	}

	$find = array ();

	if (! empty ($rex->info['Collection']['sitesearch_include_field'])) {
		$find[$rex->info['Collection']['sitesearch_include_field']] = new rEqual ($rex->info['Collection']['sitesearch_include_field'], 'yes');
	}

	$struct = $rex->getStruct ();

	if (isset ($struct['sitellite_status'])) {
		$find['sitellite_status'] = new rEqual ('sitellite_status', 'approved');
	}

	if (isset ($struct['sitellite_access'])) {
		$find['sitellite_access'] = new rEqual ('sitellite_access', 'public');
	}

	foreach ($rex->getList ($find) as $obj) {
		$urls[] = 'http://' . conf ('Site', 'domain') . '/index/' . sprintf ($rex->info['Collection']['sitesearch_url'], $obj->{$rex->key});
	}
}

$out = '<?xml version="1.0" encoding="UTF-8"?' . ">\n";
$out .= '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">' . "\n";

foreach ($urls as $url) {
	$out .= '<url><loc>' . $url . "</loc></url>\n";
}

$out .= '</urlset>';

umask (0000);

if (extension_loaded ('zlib')) {
	$fp = gzopen ('googlesitemap.xml.gz', 'w');
	gzwrite ($fp, $out);
	gzclose ($fp);
} else {
	$fp = fopen ('googlesitemap.xml', 'w');
	fwrite ($fp, $out);
	fclose ($fp);
}

?>