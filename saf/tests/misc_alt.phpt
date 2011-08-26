--TEST--
saf.Misc.Alt
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Misc.Alt');

// constructor method

$alt = new Alt ('$vals', '$val2', '$val3');

// next() method

var_dump ($alt->next ());

// reset() method

var_dump ($alt->reset ());

?>
--EXPECT--
