--TEST--
saf.Misc.Lister
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Misc.Lister');

// constructor method

$lister = new Lister;

// get() method

var_dump ($lister->get ('$key'));

// set() method

var_dump ($lister->set ('$key', '$value'));

// count() method

var_dump ($lister->count ());

// reset() method

var_dump ($lister->reset ());

// iterate() method

var_dump ($lister->iterate ());

// walk() method

var_dump ($lister->walk ('$call'));

?>
--EXPECT--
