--TEST--
saf.MailForm.Widget.Imagechooser
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Imagechooser');

// constructor method

$mf_widget_imagechooser = new MF_Widget_imagechooser ('$name');

// _link() method

var_dump ($mf_widget_imagechooser->_link ());

// display() method

var_dump ($mf_widget_imagechooser->display ('$generate_html'));

?>
--EXPECT--
