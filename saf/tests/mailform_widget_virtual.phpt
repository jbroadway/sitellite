--TEST--
saf.MailForm.Widget.Virtual
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Virtual');

// constructor method

$mf_widget_virtual = new MF_Widget_virtual ('$name');

// validate() method

var_dump ($mf_widget_virtual->validate ('$value', '$form', '$cgi'));

// setValue() method

var_dump ($mf_widget_virtual->setValue ('$key', '$value'));

// getValue() method

var_dump ($mf_widget_virtual->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_virtual->display ('$generate_html'));

?>
--EXPECT--
