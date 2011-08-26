--TEST--
saf.MailForm.Widget.Access
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Access');

// constructor method

$mf_widget_access = new MF_Widget_access ('$name');

// setAllowed() method

var_dump ($mf_widget_access->setAllowed ('$allowed'));

// display() method

var_dump ($mf_widget_access->display ('$generate_html'));

?>
--EXPECT--
