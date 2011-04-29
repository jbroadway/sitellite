--TEST--
saf.MailForm.Widget.Calendar
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Calendar');

// constructor method

$mf_widget_calendar = new MF_Widget_calendar ('$name');

// displayValue() method

var_dump ($mf_widget_calendar->displayValue ());

// js2phpFormat() method

var_dump ($mf_widget_calendar->js2phpFormat ('$date'));

// display() method

var_dump ($mf_widget_calendar->display ('$generate_html'));

?>
--EXPECT--
