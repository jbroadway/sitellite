--TEST--
saf.MailForm.Widget.Team
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Widget.Team');

// constructor method

$mf_widget_team = new MF_Widget_team;

// getOwner() method

var_dump ($mf_widget_team->getOwner ());

// display() method

var_dump ($mf_widget_team->display ('$generate_html'));

?>
--EXPECT--
