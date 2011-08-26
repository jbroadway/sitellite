--TEST--
saf.Database.BDB
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Database.BDB');

// constructor method

$bdb = new BDB ('$path', '$mode', '$handler', '$persistent');

// close() method

var_dump ($bdb->close ());

// delete() method

var_dump ($bdb->delete ('$key'));

// delete_all() method

var_dump ($bdb->delete_all ());

// exists() method

var_dump ($bdb->exists ('$key'));

// fetch() method

var_dump ($bdb->fetch ('$key'));

// firstkey() method

var_dump ($bdb->firstkey ());

// insert() method

var_dump ($bdb->insert ('$key', '$value'));

// nextkey() method

var_dump ($bdb->nextkey ());

// optimize() method

var_dump ($bdb->optimize ());

// replace() method

var_dump ($bdb->replace ('$key', '$value'));

// sync() method

var_dump ($bdb->sync ());

?>
--EXPECT--
