--TEST--
saf.MailForm.Widget.Mref
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Mref');

// constructor method

$mf_widget_mref = new MF_Widget_mref ('$name');

// getNumRows() method

var_dump ($mf_widget_mref->getNumRows ());

// getData() method

var_dump ($mf_widget_mref->getData ('$val', '$dashes'));

// display() method

var_dump ($mf_widget_mref->display ('$generate_html'));

?>
--EXPECT--
