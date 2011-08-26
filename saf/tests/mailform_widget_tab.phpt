--TEST--
saf.MailForm.Widget.Tab
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Tab');

// constructor method

$mf_widget_tab = new MF_Widget_tab ('$name');

// validate() method

var_dump ($mf_widget_tab->validate ('$value', '$form', '$cgi'));

// getValue() method

var_dump ($mf_widget_tab->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_tab->display ('$generate_html'));

?>
--EXPECT--
