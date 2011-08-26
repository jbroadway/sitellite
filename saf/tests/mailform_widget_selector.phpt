--TEST--
saf.MailForm.Widget.Selector
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Selector');

// constructor method

$mf_widget_selector = new MF_Widget_selector ('$name');

// getList() method

var_dump ($mf_widget_selector->getList ());

// setValue() method

var_dump ($mf_widget_selector->setValue ('$value', '$inner_component'));

// getValue() method

var_dump ($mf_widget_selector->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_selector->display ('$generate_html'));

?>
--EXPECT--
