--TEST--
saf.GUI.DropMenu
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.GUI.DropMenu');

// constructor method

$dropmenu = new DropMenu ('$name', '$xpos', '$ypos', '$menuWidth', '$lineHeight', '$template', '$direction');

// addItem() method

var_dump ($dropmenu->addItem ('$text', '$link'));

// write() method

var_dump ($dropmenu->write ('$show'));

// getList() method

var_dump ($dropmenu->getList ());

?>
--EXPECT--
