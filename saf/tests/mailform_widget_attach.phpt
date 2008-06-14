--TEST--
saf.MailForm.Widget.Attach
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Attach');

// constructor method

$mf_widget_attach = new MF_Widget_attach;

// buttons() method

var_dump ($mf_widget_attach->buttons ());

// validate() method

var_dump ($mf_widget_attach->validate ());

// getValue() method

var_dump ($mf_widget_attach->getValue ());

// display() method

var_dump ($mf_widget_attach->display ('$generate_html'));

?>
--EXPECT--
