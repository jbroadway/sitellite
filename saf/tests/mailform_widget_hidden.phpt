--TEST--
saf.MailForm.Widget.Hidden
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Hidden');

// constructor method

$mf_widget_hidden = new MF_Widget_hidden;

// display() method

var_dump ($mf_widget_hidden->display ('$generate_html'));

?>
--EXPECT--
