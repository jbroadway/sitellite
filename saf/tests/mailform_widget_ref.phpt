--TEST--
saf.MailForm.Widget.Ref
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Ref');

// constructor method

$mf_widget_ref = new MF_Widget_ref ('$name');

// getNumRows() method

var_dump ($mf_widget_ref->getNumRows ());

// getData() method

var_dump ($mf_widget_ref->getData ('$val', '$dashes'));

// display() method

var_dump ($mf_widget_ref->display ('$generate_html'));

?>
--EXPECT--
