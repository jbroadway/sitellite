--TEST--
saf.Database.PropertySet
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Database.PropertySet');

// constructor method

$propertyset = new PropertySet ('$collection', '$entity');

// get() method

var_dump ($propertyset->get ('$property'));

// add() method

var_dump ($propertyset->add ('$property', '$data_value'));

// update() method

var_dump ($propertyset->update ('$property', '$data_value'));

// set() method

var_dump ($propertyset->set ('$property', '$data_value'));

// delete() method

var_dump ($propertyset->delete ('$property'));

?>
--EXPECT--
