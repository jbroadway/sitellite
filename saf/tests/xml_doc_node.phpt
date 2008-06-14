--TEST--
saf.XML.Doc.Node
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.Doc.Node');

// constructor method

$xmlnode = new XMLNode ('$name', '$content', '$number', '$parent');

// addChild() method

var_dump ($xmlnode->addChild ('$name', '$content'));

// setAttribute() method

var_dump ($xmlnode->setAttribute ('$name', '$value'));

// setCallback() method

var_dump ($xmlnode->setCallback ('$startFunction', '$endFunction', '$obj'));

// propagateCallback() method

var_dump ($xmlnode->propagateCallback ('$startFunction', '$endFunction', '$obj'));

// write() method

var_dump ($xmlnode->write ('$level'));

// query() method

var_dump ($xmlnode->query ('$path', '$ref'));

// path() method

var_dump ($xmlnode->path ());

// makeObj() method

var_dump ($xmlnode->makeObj ());

// makeRefObj() method

var_dump ($xmlnode->makeRefObj ());

// _makeMenu() method

var_dump ($xmlnode->_makeMenu ('$menu', '$parent'));

// makeMenu() method

var_dump ($xmlnode->makeMenu ());

?>
--EXPECT--
