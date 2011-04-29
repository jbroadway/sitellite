--TEST--
saf.MailForm.Widget.Tie
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Tie');

// constructor method

$mf_widget_tie = new MF_Widget_tie ('$name');

// setValues() method

var_dump ($mf_widget_tie->setValues ('$name', '$values'));

// includeJS() method

var_dump ($mf_widget_tie->includeJS ());

// display() method

var_dump ($mf_widget_tie->display ('$generate_html'));

?>
--EXPECT--
