--TEST--
saf.MailForm.Widget.Checkbox
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Checkbox');

// constructor method

$mf_widget_checkbox = new MF_Widget_checkbox ('$name');

// setValue() method

var_dump ($mf_widget_checkbox->setValue ('$value', '$inner_component'));

// getValue() method

var_dump ($mf_widget_checkbox->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_checkbox->display ('$generate_html'));

?>
--EXPECT--
