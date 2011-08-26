--TEST--
saf.Session.Store.PHP
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Session.Store.PHP');

// constructor method

$sessionstore_php = new SessionStore_PHP;

// start() method

var_dump ($sessionstore_php->start ('$id'));

// get() method

var_dump ($sessionstore_php->get ('$name'));

// set() method

var_dump ($sessionstore_php->set ('$name', '$value'));

// save() method

var_dump ($sessionstore_php->save ());

// close() method

var_dump ($sessionstore_php->close ());

?>
--EXPECT--
