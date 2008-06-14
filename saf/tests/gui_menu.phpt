--TEST--
saf.GUI.Menu
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.GUI.Menu');

// constructor method

$menu = new Menu ('$table', '$idcol', '$showcol', '$refcol', '$listcol', '$sectioncol', '$templatecol');

// getTree() method

var_dump ($menu->getTree ());

// findParent() method

var_dump ($menu->findParent ('$ref'));

// addItem() method

var_dump ($menu->addItem ('$id', '$title', '$ref', '$sect', '$template'));

// loadConfig() method

var_dump ($menu->loadConfig ('$file'));

// makeConfig() method

var_dump ($menu->makeConfig ());

// display() method

var_dump ($menu->display ('$mode', '$tplt', '$recursive'));

// trail() method

var_dump ($menu->trail ('$id', '$tplt', '$home', '$separator'));

// includeJavaScript() method

var_dump ($menu->includeJavaScript ());

// homeLink() method

var_dump ($menu->homeLink ('$tplt'));

// section() method

var_dump ($menu->section ('$id', '$tplt', '$open', '$recursive', '$skip'));

// countChildren() method

var_dump ($menu->countChildren ('$id'));

// getChildren() method

var_dump ($menu->getChildren ('$id'));

// clear() method

var_dump ($menu->clear ());

// getSections() method

var_dump ($menu->getSections ('$item'));

// menu_get_sections() function

var_dump (menu_get_sections ('$item'));

// menu_is_child_of() function

var_dump (menu_is_child_of ('$child', '$parent'));

?>
--EXPECT--
