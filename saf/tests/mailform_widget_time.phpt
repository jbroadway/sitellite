--TEST--
saf.MailForm.Widget.Time
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Time');

// constructor method

$mf_widget_time = new MF_Widget_time ('$name');

// setValue() method

var_dump ($mf_widget_time->setValue ('$value', '$inner_component'));

// setDefault() method

var_dump ($mf_widget_time->setDefault ('$value'));

// getValue() method

var_dump ($mf_widget_time->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_time->display ('$generate_html'));

?>
--EXPECT--
