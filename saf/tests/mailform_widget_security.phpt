--TEST--
saf.MailForm.Widget.Security
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Security');

// constructor method

$mf_widget_security = new MF_Widget_security ('$name');

// verify() method

var_dump ($mf_widget_security->verify ());

// display() method

var_dump ($mf_widget_security->display ('$generate_html'));

// mailform_widget_security_verify() function

var_dump (mailform_widget_security_verify ('$vals'));

?>
--EXPECT--
