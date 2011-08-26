--TEST--
saf.XML.Browser
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.Browser');

// constructor method

$xmlbrowser = new XMLBrowser ('$doc');

// set() method

var_dump ($xmlbrowser->set ('$params'));

// browse() method

var_dump ($xmlbrowser->browse ('$path', '$visible', '$offset', '$limit'));

// add() method

var_dump ($xmlbrowser->add ('$parentPath', '$types', '$template'));

// editable() method

var_dump ($xmlbrowser->editable ('$path', '$types'));

// delete() method

var_dump ($xmlbrowser->delete ('$paths'));

?>
--EXPECT--
