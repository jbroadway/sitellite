--TEST--
saf.Session.Store
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Session.Store');

// constructor method

$sessionstore = new SessionStore;

// setProperties() method

var_dump ($sessionstore->setProperties ('$properties'));

// start() method

var_dump ($sessionstore->start ('$id'));

// get() method

var_dump ($sessionstore->get ('$name'));

// set() method

var_dump ($sessionstore->set ('$name', '$value'));

// save() method

var_dump ($sessionstore->save ());

// close() method

var_dump ($sessionstore->close ());

?>
--EXPECT--
