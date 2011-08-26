--TEST--
saf.MailForm.Widget.Date
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Date');

// constructor method

$mf_widget_date = new MF_Widget_date ('$name');

// setValue() method

var_dump ($mf_widget_date->setValue ('$value', '$inner_component'));

// getValue() method

var_dump ($mf_widget_date->getValue ('$cgi'));

// setDefault() method

var_dump ($mf_widget_date->setDefault ('$value'));

// display() method

var_dump ($mf_widget_date->display ('$generate_html'));

?>
--EXPECT--
