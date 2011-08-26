--TEST--
saf.Database.NestedSet
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Database.NestedSet');

// constructor method

$nestedset = new NestedSet ('$table', '$key', '$root', '$left', '$right', '$order', '$level');

// create() method

var_dump ($nestedset->create ('$fields'));

// root() method

var_dump ($nestedset->root ('$val'));

// addRoot() method

var_dump ($nestedset->addRoot ('$values'));

// add() method

var_dump ($nestedset->add ('$pid', '$values'));

// edit() method

var_dump ($nestedset->edit ('$id', '$newValues'));

// delete() method

var_dump ($nestedset->delete ('$id', '$recursive'));

// move() method

var_dump ($nestedset->move ('$id', '$newParentId'));

// rename() method

var_dump ($nestedset->rename ('$id', '$newId'));

// get() method

var_dump ($nestedset->get ('$id', '$fields'));

// getRoots() method

var_dump ($nestedset->getRoots ('$fields'));

// find() method

var_dump ($nestedset->find ('$queries'));

// siblings() method

var_dump ($nestedset->siblings ('$id'));

// children() method

var_dump ($nestedset->children ('$id', '$recursive'));

// path() method

var_dump ($nestedset->path ('$id', '$fields'));

// parent() method

var_dump ($nestedset->parent ('$id', '$fields'));

// isParent() method

var_dump ($nestedset->isParent ('$pid', '$id'));

// _fields() method

var_dump ($nestedset->_fields ('$fields', '$else'));

// _where() method

var_dump ($nestedset->_where ('$queries'));

// _set() method

var_dump ($nestedset->_set ('$set'));

// _addRightNode() method

var_dump ($nestedset->_addRightNode ('$sid', '$values'));

?>
--EXPECT--
