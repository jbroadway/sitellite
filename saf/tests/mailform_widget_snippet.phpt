--TEST--
saf.MailForm.Widget.Snippet
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Snippet');

// constructor method

$mf_widget_snippet = new MF_Widget_snippet;

// getValue() method

var_dump ($mf_widget_snippet->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_snippet->display ('$generate_html'));

?>
--EXPECT--
