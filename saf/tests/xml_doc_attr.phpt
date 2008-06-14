--TEST--
saf.XML.Doc.Attr
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.Doc.Attr');

// constructor method

$xmlattr = new XMLAttr ('$name', '$value');

// write() method

var_dump ($xmlattr->write ('$space'));

?>
--EXPECT--
