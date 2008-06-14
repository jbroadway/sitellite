--TEST--
saf.MailForm.Widget.Richtext
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Richtext');

// constructor method

$mf_widget_richtext = new MF_Widget_richtext;

// clean() method

var_dump ($mf_widget_richtext->clean ('$input'));

// display() method

var_dump ($mf_widget_richtext->display ('$generate_html'));

?>
--EXPECT--
