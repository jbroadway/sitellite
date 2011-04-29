--TEST--
saf.XML.Doc
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.Doc');

// constructor method

$xmldoc = new XMLDoc ('$version', '$encoding');

// addRoot() method

var_dump ($xmldoc->addRoot ('$name'));

// write() method

var_dump ($xmldoc->write ('$level'));

// query() method

var_dump ($xmldoc->query ('$path', '$ref'));

// makeDoc() method

var_dump ($xmldoc->makeDoc ('$obj', '$toptag'));

// writeToFile() method

var_dump ($xmldoc->writeToFile ('$file', '$level'));

// makeMenu() method

var_dump ($xmldoc->makeMenu ());

// makeObj() method

var_dump ($xmldoc->makeObj ());

// makeRefObj() method

var_dump ($xmldoc->makeRefObj ());

// cache() method

var_dump ($xmldoc->cache ('$file'));

// propagateCallback() method

var_dump ($xmldoc->propagateCallback ('$startFunction', '$endFunction', '$obj'));

?>
--EXPECT--
