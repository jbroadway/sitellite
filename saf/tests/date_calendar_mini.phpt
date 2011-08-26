--TEST--
saf.Date.Calendar.Mini
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Date.Calendar.Mini');

// constructor method

$minical = new MiniCal ('$mc');

// addLink() method

var_dump ($minical->addLink ('$day', '$link'));

// prevDate() method

var_dump ($minical->prevDate ());

// nextDate() method

var_dump ($minical->nextDate ());

// monthName() method

var_dump ($minical->monthName ());

// isActive() method

var_dump ($minical->isActive ('$r', '$c'));

// isLink() method

var_dump ($minical->isLink ('$day'));

// isCurrent() method

var_dump ($minical->isCurrent ('$day'));

// isWeekend() method

var_dump ($minical->isWeekend ('$day'));

// render() method

var_dump ($minical->render ());

?>
--EXPECT--
