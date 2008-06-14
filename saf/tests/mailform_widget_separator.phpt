--TEST--
saf.MailForm.Widget.Separator
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Separator');

// constructor method

$mf_widget_separator = new MF_Widget_separator ('$name');

// validate() method

var_dump ($mf_widget_separator->validate ('$value', '$form', '$cgi'));

// getValue() method

var_dump ($mf_widget_separator->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_separator->display ('$generate_html'));

?>
--EXPECT--
