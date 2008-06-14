--TEST--
saf.XML.RelaxNG.Validator
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.RelaxNG.Validator');

// constructor method

$rngvalidator = new RNGValidator;

// getRules() method

var_dump ($rngvalidator->getRules ('$schema'));

// getRule() method

var_dump ($rngvalidator->getRule ('$path'));

?>
--EXPECT--
