--TEST--
saf.MailForm.Widget.Allow
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Allow');

// constructor method

$mf_widget_allow = new MF_Widget_allow ('$name');

// getTables() method

var_dump ($mf_widget_allow->getTables ());

// getDirs() method

var_dump ($mf_widget_allow->getDirs ());

// getMods() method

var_dump ($mf_widget_allow->getMods ());

// getStatus() method

var_dump ($mf_widget_allow->getStatus ());

// getAccess() method

var_dump ($mf_widget_allow->getAccess ());

// display() method

var_dump ($mf_widget_allow->display ('$generate_html'));

?>
--EXPECT--
