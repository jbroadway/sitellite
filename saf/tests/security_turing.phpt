--TEST--
saf.Security.Turing
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Security.Turing');

// constructor method

$security_turing = new Security_Turing;

// generateImage() method

var_dump ($security_turing->generateImage ('$token'));

// makeTest() method

var_dump ($security_turing->makeTest ());

// verify() method

var_dump ($security_turing->verify ('$input', '$hash'));

// constructor method

$turingtest = new TuringTest;

?>
--EXPECT--
