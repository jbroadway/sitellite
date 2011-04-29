--TEST--
saf.GUI.MenuItem
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.GUI.MenuItem');

// constructor method

$menuitem = new MenuItem ('$id', '$title');

// addChild() method

var_dump ($menuitem->addChild ('$id', '$title'));

// trail() method

var_dump ($menuitem->trail ());

// display() method

var_dump ($menuitem->display ('$mode', '$tplt', '$recursive'));

?>
--EXPECT--
