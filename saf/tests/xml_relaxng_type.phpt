--TEST--
saf.XML.RelaxNG.Type
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.RelaxNG.Type');

// constructor method

$rngtype = new RNGType ('$ns');

// validate() method

var_dump ($rngtype->validate ('$value'));

?>
--EXPECT--
