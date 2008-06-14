--TEST--
saf.MailForm.Widget.Password
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Password');

// constructor method

$mf_widget_password = new MF_Widget_password;

// encrypt() method

var_dump ($mf_widget_password->encrypt ('$value', '$salt'));

// verify() method

var_dump ($mf_widget_password->verify ('$input', '$encrypted'));

// makeStrong() method

var_dump ($mf_widget_password->makeStrong ());

// generate() method

var_dump ($mf_widget_password->generate ('$length'));

// validate() method

var_dump ($mf_widget_password->validate ('$value', '$form', '$cgi'));

// display() method

var_dump ($mf_widget_password->display ('$generate_html'));

?>
--EXPECT--
