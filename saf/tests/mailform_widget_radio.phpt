--TEST--
saf.MailForm.Widget.Radio
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Radio');

// constructor method

$mf_widget_radio = new MF_Widget_radio ('$name');

// display() method

var_dump ($mf_widget_radio->display ('$generate_html'));

?>
--EXPECT--
