--TEST--
saf.MailForm.Widget.Submit
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Submit');

// constructor method

$mf_widget_submit = new MF_Widget_submit;

// display() method

var_dump ($mf_widget_submit->display ('$generate_html'));

?>
--EXPECT--
