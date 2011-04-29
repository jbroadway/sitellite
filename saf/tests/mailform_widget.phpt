--TEST--
saf.MailForm.Widget
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget');

// constructor method

$mf_widget = new MF_Widget ('$name');

// validation() method

var_dump ($mf_widget->validation ('$rule'));

// addRule() method

var_dump ($mf_widget->addRule ('$rule', '$msg'));

// validate() method

var_dump ($mf_widget->validate ('$value', '$form', '$cgi'));

// setValues() method

var_dump ($mf_widget->setValues ('$key', '$value'));

// setValue() method

var_dump ($mf_widget->setValue ('$value', '$inner_component'));

// getValue() method

var_dump ($mf_widget->getValue ('$cgi'));

// setDefault() method

var_dump ($mf_widget->setDefault ('$value'));

// display() method

var_dump ($mf_widget->display ('$generate_html'));

// attr() method

var_dump ($mf_widget->attr ('$key', '$value'));

// unsetAttr() method

var_dump ($mf_widget->unsetAttr ('$key'));

// getAttrs() method

var_dump ($mf_widget->getAttrs ());

// invalid() method

var_dump ($mf_widget->invalid ());

// changeType() method

var_dump ($mf_widget->changeType ('$newType', '$extra'));

?>
--EXPECT--
