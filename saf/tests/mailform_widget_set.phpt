--TEST--
saf.MailForm.Widget.Set
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Set');

// constructor method

$mf_widget_set = new MF_Widget_set ('$name');

// setValue() method

var_dump ($mf_widget_set->setValue ('$value', '$inner_component'));

// setValues() method

var_dump ($mf_widget_set->setValues ('$table'));

// getValue() method

var_dump ($mf_widget_set->getValue ('$cgi'));

// setDefault() method

var_dump ($mf_widget_set->setDefault ('$value'));

// display() method

var_dump ($mf_widget_set->display ('$generate_html'));

?>
--EXPECT--
