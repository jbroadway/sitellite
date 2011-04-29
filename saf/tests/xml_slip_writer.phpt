--TEST--
saf.XML.SLiP.Writer
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.SLiP.Writer');

// constructor method

$slipwriter = new SLiPWriter;

// _start() method

var_dump ($slipwriter->_start ('$node', '$level'));

?>
--EXPECT--
