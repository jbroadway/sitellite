--TEST--
saf.MailForm.Widget.Textarea
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Textarea');

// constructor method

$mf_widget_textarea = new MF_Widget_textarea;

// display() method

var_dump ($mf_widget_textarea->display ('$generate_html'));

?>
--EXPECT--
