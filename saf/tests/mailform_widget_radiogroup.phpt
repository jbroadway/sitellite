--TEST--
saf.MailForm.Widget.Radiogroup
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Radiogroup');

// constructor method

$mf_widget_radiogroup = new MF_Widget_radiogroup ('$name', '$value');

// addButton() method

var_dump ($mf_widget_radiogroup->addButton ('$name', '$value'));

// validate() method

var_dump ($mf_widget_radiogroup->validate ('$value', '$form', '$cgi'));

// getValue() method

var_dump ($mf_widget_radiogroup->getValue ('$cgi'));

// display() method

var_dump ($mf_widget_radiogroup->display ('$generate_html'));

?>
--EXPECT--
