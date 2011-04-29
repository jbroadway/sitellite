--TEST--
saf.Date.vCalendar.vCalProperty
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Date.vCalendar.vCalProperty');

// constructor method

$vcalproperty = new vCalProperty ('$name', '$value', '$parameters');

// addParameter() method

var_dump ($vcalproperty->addParameter ('$key', '$value'));

// write() method

var_dump ($vcalproperty->write ());

?>
--EXPECT--
