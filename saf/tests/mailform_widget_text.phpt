--TEST--
saf.MailForm.Widget.Text
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Text');

// constructor method

$mf_widget_text = new MF_Widget_text;

// display() method

var_dump ($mf_widget_text->display ('$generate_html'));

?>
--EXPECT--
