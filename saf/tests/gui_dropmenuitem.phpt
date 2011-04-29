--TEST--
saf.GUI.DropMenuItem
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.GUI.DropMenuItem');

// constructor method

$dropmenuitem = new DropMenuItem ('$text', '$link', '$xpos', '$ypos', '$menuWidth', '$lineHeight', '$menu_tpl', '$link_tpl');

// write() method

var_dump ($dropmenuitem->write ('$show'));

// addChild() method

var_dump ($dropmenuitem->addChild ('$name'));

?>
--EXPECT--
