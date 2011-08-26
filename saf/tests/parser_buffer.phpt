--TEST--
saf.Parser.Buffer
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Parser.Buffer');

// constructor method

$buffer = new Buffer;

// set() method

var_dump ($buffer->set ('$name', '$data'));

// append() method

var_dump ($buffer->append ('$name', '$data'));

// prepend() method

var_dump ($buffer->prepend ('$name', '$data'));

// clear() method

var_dump ($buffer->clear ('$name'));

// get() method

var_dump ($buffer->get ('$name'));

// getAll() method

var_dump ($buffer->getAll ());

?>
--EXPECT--
