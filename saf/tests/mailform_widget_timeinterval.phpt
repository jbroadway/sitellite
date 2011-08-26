--TEST--
saf.MailForm.Widget.Timeinterval
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Timeinterval');

// constructor method

$mf_widget_timeinterval = new MF_Widget_timeinterval ('$name');

// setValue() method

var_dump ($mf_widget_timeinterval->setValue ('$value', '$inner_component'));

// parseTime() method

var_dump ($mf_widget_timeinterval->parseTime ('$time'));

// makeTime() method

var_dump ($mf_widget_timeinterval->makeTime ('$hour', '$minute', '$ampm'));

// getValue() method

var_dump ($mf_widget_timeinterval->getValue ('$cgi'));

// setDefault() method

var_dump ($mf_widget_timeinterval->setDefault ('$value'));

// display() method

var_dump ($mf_widget_timeinterval->display ('$generate_html'));

?>
--EXPECT--
