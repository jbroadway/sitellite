--TEST--
saf.MailForm.Widget.Status
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Status');

// constructor method

$mf_widget_status = new MF_Widget_status ('$name');

// setAllowed() method

var_dump ($mf_widget_status->setAllowed ('$allowed'));

// display() method

var_dump ($mf_widget_status->display ('$generate_html'));

?>
--EXPECT--
