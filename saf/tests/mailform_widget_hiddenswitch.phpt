--TEST--
saf.MailForm.Widget.Hiddenswitch
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Hiddenswitch');

// constructor method

$mf_widget_hiddenswitch = new MF_Widget_hiddenswitch ('$name');

// getValue() method

var_dump ($mf_widget_hiddenswitch->getValue ('$cgi'));

?>
--EXPECT--
