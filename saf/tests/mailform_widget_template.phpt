--TEST--
saf.MailForm.Widget.Template
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Template');

// constructor method

$mf_widget_template = new MF_Widget_template ('$name');

// validate() method

var_dump ($mf_widget_template->validate ('$value', '$form', '$cgi'));

// setValues() method

var_dump ($mf_widget_template->setValues ('$key', '$value'));

// setValue() method

var_dump ($mf_widget_template->setValue ('$key', '$value'));

// getValue() method

var_dump ($mf_widget_template->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_template->display ('$generate_html'));

?>
--EXPECT--
