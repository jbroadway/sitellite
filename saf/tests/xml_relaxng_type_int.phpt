--TEST--
saf.XML.RelaxNG.Type.Int
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.RelaxNG.Type.Int');

// constructor method

$rngtype_int = new RNGType_int;

// validate() method

var_dump ($rngtype_int->validate ('$value'));

?>
--EXPECT--
