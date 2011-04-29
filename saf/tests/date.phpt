--TEST--
saf.Date
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Date');

// constructor method

$date = new Date;

// toUnix() method

var_dump ($date->toUnix ('$date'));

// compare() method

var_dump ($date->compare ('$date1', '$date2', '$equality_range', '$range_forward'));

// local() method

var_dump ($date->local ('$date', '$offset', '$format'));

// format() method

var_dump ($date->format ('$date', '$format'));

// time() method

var_dump ($date->time ('$time', '$format'));

// timestamp() method

var_dump ($date->timestamp ('$timestamp', '$format'));

// add() method

var_dump ($date->add ('$date', '$amount'));

// subtract() method

var_dump ($date->subtract ('$date', '$amount'));

// roundTime() method

var_dump ($date->roundTime ('$time', '$interval'));

// convert() method

var_dump ($date->convert ('$frmt'));

?>
--EXPECT--
