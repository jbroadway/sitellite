--TEST--
saf.XML.RelaxNG.Type.Text
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.RelaxNG.Type.Text');

// constructor method

$rngtype_text = new RNGType_text;

// validate() method

var_dump ($rngtype_text->validate ('$value'));

?>
--EXPECT--
