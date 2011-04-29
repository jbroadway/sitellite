--TEST--
saf.XML.RSS
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.RSS');

// constructor method

$rss = new RSS;

// getUrl() method

var_dump ($rss->getUrl ('$url'));

// getXsl() method

var_dump ($rss->getXsl ('$file'));

// process() method

var_dump ($rss->process ('$xsldata', '$xmldata', '$cache_dir', '$cache_duration', '$cache_file'));

// makeDoc() method

var_dump ($rss->makeDoc ('$version'));

// setDoctype() method

var_dump ($rss->setDoctype ('$doctype'));

// addChannel() method

var_dump ($rss->addChannel ('$title', '$link', '$description', '$language'));

// addItem() method

var_dump ($rss->addItem ('$title', '$link', '$description'));

// write() method

var_dump ($rss->write ());

?>
--EXPECT--
