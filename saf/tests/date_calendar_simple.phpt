--TEST--
saf.Date.Calendar.Simple
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Date.Calendar.Simple');

// constructor method

$simplecal = new SimpleCal ('$mc');

// addLink() method

var_dump ($simplecal->addLink ('$day', '$text', '$link', '$important', '$alt', '$pre'));

// addHTML() method

var_dump ($simplecal->addHTML ('$day', '$html'));

// prevDate() method

var_dump ($simplecal->prevDate ());

// nextDate() method

var_dump ($simplecal->nextDate ());

// monthName() method

var_dump ($simplecal->monthName ());

// prevMonth() method

var_dump ($simplecal->prevMonth ());

// nextMonth() method

var_dump ($simplecal->nextMonth ());

// isActive() method

var_dump ($simplecal->isActive ('$r', '$c'));

// isLink() method

var_dump ($simplecal->isLink ('$day'));

// isCurrent() method

var_dump ($simplecal->isCurrent ('$day'));

// isWeekend() method

var_dump ($simplecal->isWeekend ('$day'));

// weekday() method

var_dump ($simplecal->weekday ('$day'));

// weekdaySun() method

var_dump ($simplecal->weekdaySun ());

// weekdayMon() method

var_dump ($simplecal->weekdayMon ());

// weekdayTue() method

var_dump ($simplecal->weekdayTue ());

// weekdayWed() method

var_dump ($simplecal->weekdayWed ());

// weekdayThu() method

var_dump ($simplecal->weekdayThu ());

// weekdayFri() method

var_dump ($simplecal->weekdayFri ());

// weekdaySat() method

var_dump ($simplecal->weekdaySat ());

// render() method

var_dump ($simplecal->render ());

?>
--EXPECT--
