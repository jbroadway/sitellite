--TEST--
saf.MailForm.Widget.Info
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Info');

// constructor method

$mf_widget_info = new MF_Widget_info;

// display() method

var_dump ($mf_widget_info->display ('$generate_html'));

?>
--EXPECT--
