--TEST--
saf.MailForm.Widget.Datetimeinterval
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Datetimeinterval');

// constructor method

$mf_widget_datetimeinterval = new MF_Widget_datetimeinterval ('$name');

// setValue() method

var_dump ($mf_widget_datetimeinterval->setValue ('$value', '$inner_component'));

// getValue() method

var_dump ($mf_widget_datetimeinterval->getValue ('$cgi'));

// setDefault() method

var_dump ($mf_widget_datetimeinterval->setDefault ('$value'));

// display() method

var_dump ($mf_widget_datetimeinterval->display ('$generate_html'));

?>
--EXPECT--
