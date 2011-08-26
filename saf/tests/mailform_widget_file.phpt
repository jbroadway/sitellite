--TEST--
saf.MailForm.Widget.File
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.File');

// constructor method

$mf_widget_file = new MF_Widget_file ('$name');

// display() method

var_dump ($mf_widget_file->display ('$generate_html'));

// move() method

var_dump ($mf_widget_file->move ('$path', '$fname', '$cgi'));

?>
--EXPECT--
