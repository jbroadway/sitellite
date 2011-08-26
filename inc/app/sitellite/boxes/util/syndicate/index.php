<?php

// BEGIN KEEPOUT CHECKING
// Add these lines to the very top of any file you don't want people to
// be able to access directly.
if (! defined ('SAF_VERSION')) {
  header ('HTTP/1.1 404 Not Found');
  echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
		. "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
		. "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
		. $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
  exit;
}
// END KEEPOUT CHECKING

// import any object we need from the global namespace
global $cache;

// box logic begins here

if (empty ($parameters['url'])) {
	return;
} else {
	$url = $parameters['url'];
}

if (empty ($parameters['duration'])) {
	$duration = $box['Custom']['duration'];
} else {
	$duration = $parameters['duration'];
}


if ($cache->expired ($cache->serialize ($url), $duration)) {

	// re-cache the document

	$xmldata = @join ('', @file ($url));

	loader_import ('saf.XML.Sloppy');
	$sloppy = new SloppyDOM;
	$doc = $sloppy->parse ($xmldata);

	if (! $doc) {
		$this->error = $sloppy->error;
		return;
	}

	$cache->file ($url, serialize ($doc));

} else {

	// fetch document from cache

	loader_import ('saf.XML.Doc');
	$doc = unserialize ($cache->show ($url));

}

// display the document

$root = $doc->root->name;

//$menu = $doc->makeMenu ();
//echo $menu->display ('html', '{title} - {content}');
//return;

foreach ($doc->query ('/' . $root . '/channel') as $channel) {

	$channel_object = $channel->makeObj ();

	echo template_simple ($box['Custom']['header'], $channel_object);

	$res = $channel->query ('/channel/item');
	if (! is_array ($res)) {
		$res = $doc->query ('/' . $root . '/item');
	}
	if (is_array ($res)) {
		foreach ($res as $item) {
			echo template_simple ($box['Custom']['template'], $item->makeObj ());
		}
	}

	echo template_simple ($box['Custom']['footer'], $channel_object);

}

if ($box['context'] == 'action') {
	exit;
}

?>