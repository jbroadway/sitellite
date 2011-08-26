--TEST--
saf.MailForm.Widget.Dirlist
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Dirlist');

// constructor method

$mf_widget_dirlist = new MF_Widget_dirlist ('$name');

// setValue() method

var_dump ($mf_widget_dirlist->setValue ('$value', '$inner_component'));

// getValue() method

var_dump ($mf_widget_dirlist->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_dirlist->display ('$generate_html'));

?>
--EXPECT--
