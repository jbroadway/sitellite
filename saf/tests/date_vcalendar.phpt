--TEST--
saf.Date.vCalendar
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Date.vCalendar');

// constructor method

$vcal = new vCal ('$parseData');

// parse() method

var_dump ($vcal->parse ('$data', '$tag'));

// parseLine() method

var_dump ($vcal->parseLine ('$data'));

// splitIntoKeys() method

var_dump ($vcal->splitIntoKeys ('$data'));

// unfold() method

var_dump ($vcal->unfold ('$data'));

// fold() method

var_dump ($vcal->fold ('$data'));

// quote() method

var_dump ($vcal->quote ('$data'));

// addEvent() method

var_dump ($vcal->addEvent ('$type', '$properties'));

// addProperty() method

var_dump ($vcal->addProperty ('$name', '$value', '$parameters'));

// write() method

var_dump ($vcal->write ());

?>
--EXPECT--
