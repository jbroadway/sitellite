--TEST--
saf.MailForm.Widget.Datetime
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Datetime');

// constructor method

$mf_widget_datetime = new MF_Widget_datetime ('$name');

// setValue() method

var_dump ($mf_widget_datetime->setValue ('$value', '$inner_component'));

// setDefault() method

var_dump ($mf_widget_datetime->setDefault ('$value'));

// getValue() method

var_dump ($mf_widget_datetime->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_datetime->display ('$generate_html'));

?>
--EXPECT--
