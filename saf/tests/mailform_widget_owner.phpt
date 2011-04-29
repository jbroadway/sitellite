--TEST--
saf.MailForm.Widget.Owner
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Owner');

// constructor method

$mf_widget_owner = new MF_Widget_owner;

// display() method

var_dump ($mf_widget_owner->display ('$generate_html'));

?>
--EXPECT--
