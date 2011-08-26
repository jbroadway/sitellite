--TEST--
saf.Misc.Shorthand
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Misc.Shorthand');

// constructor method

$phpshorthand = new PHPShorthand;

// replaceGlobals() method

var_dump ($phpshorthand->replaceGlobals ('$newReg', '$replace'));

// addSprintf() method

var_dump ($phpshorthand->addSprintf ('$data', '$quote'));

// transform() method

var_dump ($phpshorthand->transform ('$data'));

?>
--EXPECT--
