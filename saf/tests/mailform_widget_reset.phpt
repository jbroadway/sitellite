--TEST--
saf.MailForm.Widget.Reset
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Reset');

// constructor method

$mf_widget_reset = new MF_Widget_reset ('$name');

// display() method

var_dump ($mf_widget_reset->display ('$generate_html'));

?>
--EXPECT--
