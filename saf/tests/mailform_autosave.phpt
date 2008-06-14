--TEST--
saf.MailForm.Autosave
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Autosave');

// constructor method

$autosave = new Autosave;

// update() method

var_dump ($autosave->update ('$vals'));

// retrieve() method

var_dump ($autosave->retrieve ('$url'));

// retrieve_all() method

var_dump ($autosave->retrieve_all ());

// count_all() method

var_dump ($autosave->count_all ());

// clear() method

var_dump ($autosave->clear ('$url'));

// clear_all() method

var_dump ($autosave->clear_all ('$url'));

// has_draft() method

var_dump ($autosave->has_draft ());

?>
--EXPECT--
