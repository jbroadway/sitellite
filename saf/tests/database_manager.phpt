--TEST--
saf.Database.Manager
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Database.Manager');

// constructor method

$dbm = new DBM;

// add() method

var_dump ($dbm->add ('$name', '$connstr', '$user', '$pass', '$persistent'));

// remove() method

var_dump ($dbm->remove ('$name', '$disconnect'));

// setCurrent() method

var_dump ($dbm->setCurrent ('$name', '$affectGlobalDB'));

// getCurrent() method

var_dump ($dbm->getCurrent ());

// dbm_add() function

var_dump (dbm_add ('$name', '$connstr', '$user', '$pass', '$persistent'));

// dbm_remove() function

var_dump (dbm_remove ('$name', '$disconnect'));

// dbm_set_current() function

var_dump (dbm_set_current ('$name', '$affectGlobalDB'));

// dbm_get_current() function

var_dump (dbm_get_current ());

?>
--EXPECT--
