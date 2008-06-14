--TEST--
saf.MailForm.Widget.Select
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Select');

// constructor method

$mf_widget_select = new MF_Widget_select;

// display() method

var_dump ($mf_widget_select->display ('$generate_html'));

?>
--EXPECT--
