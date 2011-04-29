--TEST--
saf.MailForm.Widget.Section
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Section');

// constructor method

$mf_widget_section = new MF_Widget_section ('$name');

// validate() method

var_dump ($mf_widget_section->validate ('$value', '$form', '$cgi'));

// setValue() method

var_dump ($mf_widget_section->setValue ('$key', '$value'));

// getValue() method

var_dump ($mf_widget_section->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_section->display ('$generate_html'));

?>
--EXPECT--
