--TEST--
saf.MailForm.Widget.Multiple
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Multiple');

// constructor method

$mf_widget_multiple = new MF_Widget_multiple ('$name');

// setValue() method

var_dump ($mf_widget_multiple->setValue ('$value', '$inner_component'));

// getValue() method

var_dump ($mf_widget_multiple->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_multiple->display ('$generate_html'));

?>
--EXPECT--
