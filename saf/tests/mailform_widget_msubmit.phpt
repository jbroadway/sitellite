--TEST--
saf.MailForm.Widget.Msubmit
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Msubmit');

// constructor method

$mf_widget_msubmit = new MF_Widget_msubmit ('$name', '$value');

// getButton() method

var_dump ($mf_widget_msubmit->getButton ());

// addButton() method

var_dump ($mf_widget_msubmit->addButton ('$name', '$value'));

// validate() method

var_dump ($mf_widget_msubmit->validate ('$value', '$form', '$cgi'));

// getValue() method

var_dump ($mf_widget_msubmit->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_msubmit->display ('$generate_html'));

?>
--EXPECT--
