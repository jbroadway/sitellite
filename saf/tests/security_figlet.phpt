--TEST--
saf.Security.Figlet
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Security.Figlet');

// constructor method

$security_figlet = new Security_Figlet;

// generateImage() method

var_dump ($security_figlet->generateImage ('$token'));

// makeTest() method

var_dump ($security_figlet->makeTest ());

// verify() method

var_dump ($security_figlet->verify ('$input', '$hash'));

?>
--EXPECT--
