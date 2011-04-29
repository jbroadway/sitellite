--TEST--
saf.XML.RelaxNG.Rule
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.RelaxNG.Rule');

// constructor method

$rngrule = new RNGRule ('$name', '$rule');

// addChild() method

var_dump ($rngrule->addChild ('$name', '$rule'));

// validate() method

var_dump ($rngrule->validate ('$value'));

// setType() method

var_dump ($rngrule->setType ('$type', '$ns', '$setOnAttr'));

// setAttribute() method

var_dump ($rngrule->setAttribute ('$name', '$rule'));

// display() method

var_dump ($rngrule->display ('$level'));

?>
--EXPECT--
