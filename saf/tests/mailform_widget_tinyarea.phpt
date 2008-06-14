--TEST--
saf.MailForm.Widget.Tinyarea
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Tinyarea');

// constructor method

$mf_widget_tinyarea = new MF_Widget_tinyarea;

// display() method

var_dump ($mf_widget_tinyarea->display ('$generate_html'));

?>
--EXPECT--
