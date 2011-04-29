--TEST--
saf.Date.Calendar
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Date.Calendar');

// constructor method

$calendar = new Calendar ('$date', '$showPeriod', '$beginWeekOn');

// getFirstAndLastDay() method

var_dump ($calendar->getFirstAndLastDay ('$date'));

// getXCells() method

var_dump ($calendar->getXCells ());

// fillCalendar() method

var_dump ($calendar->fillCalendar ('$sql', '$dateColumn', '$itemTemplate', '$cellTemplate', '$timeColumn'));

// makeTime() method

var_dump ($calendar->makeTime ('$hour', '$tstring'));

// makeHeader() method

var_dump ($calendar->makeHeader ('$contents', '$properties'));

?>
--EXPECT--
