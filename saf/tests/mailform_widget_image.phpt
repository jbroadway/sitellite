--TEST--
saf.MailForm.Widget.Image
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Image');

// constructor method

$mf_widget_image = new MF_Widget_image ('$name');

// getValue() method

var_dump ($mf_widget_image->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_image->display ('$generate_html'));

?>
--EXPECT--
