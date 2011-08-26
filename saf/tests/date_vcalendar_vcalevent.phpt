--TEST--
saf.Date.vCalendar.vCalEvent
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Date.vCalendar.vCalEvent');

// constructor method

$vcalevent = new vCalEvent ('$type', '$properties');

// addProperty() method

var_dump ($vcalevent->addProperty ('$name', '$value', '$parameters'));

// write() method

var_dump ($vcalevent->write ());

?>
--EXPECT--
