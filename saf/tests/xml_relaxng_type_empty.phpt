--TEST--
saf.XML.RelaxNG.Type.Empty
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.RelaxNG.Type.Empty');

// constructor method

$rngtype_empty = new RNGType_empty;

// validate() method

var_dump ($rngtype_empty->validate ('$value'));

?>
--EXPECT--
