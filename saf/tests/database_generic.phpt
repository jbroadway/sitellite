--TEST--
saf.Database.Generic
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Database.Generic');

// constructor method

$generic = new Generic ('$table', '$pkey', '$fkey', '$listFields', '$isAuto');

// _join() method

var_dump ($generic->_join ('$list', '$op'));

// _end() method

var_dump ($generic->_end ());

// translate() method

var_dump ($generic->translate ('$obj'));

// listFields() method

var_dump ($generic->listFields ('$listFields'));

// foreignKey() method

var_dump ($generic->foreignKey ('$fkey'));

// orderBy() method

var_dump ($generic->orderBy ('$orderBy'));

// groupBy() method

var_dump ($generic->groupBy ('$groupBy'));

// limit() method

var_dump ($generic->limit ('$limit'));

// offset() method

var_dump ($generic->offset ('$offset'));

// clear() method

var_dump ($generic->clear ());

// find() method

var_dump ($generic->find ('$fid'));

// getList() method

var_dump ($generic->getList ('$fid'));

// single() method

var_dump ($generic->single ());

// shift() method

var_dump ($generic->shift ());

// count() method

var_dump ($generic->count ('$fid'));

// query() method

var_dump ($generic->query ('$sql', '$bind'));

// get() method

var_dump ($generic->get ('$id'));

// add() method

var_dump ($generic->add ('$struct'));

// modify() method

var_dump ($generic->modify ('$id', '$struct'));

// remove() method

var_dump ($generic->remove ('$id'));

// makeStruct() method

var_dump ($generic->makeStruct ('$vals'));

// addRule() method

var_dump ($generic->addRule ('$name', '$rule', '$msg'));

// validate() method

var_dump ($generic->validate ('$vals'));

// setCurrent() method

var_dump ($generic->setCurrent ('$obj'));

// makeObj() method

var_dump ($generic->makeObj ());

// set() method

var_dump ($generic->set ('$name', '$value'));

// val() method

var_dump ($generic->val ('$name'));

// pkey() method

var_dump ($generic->pkey ());

// exists() method

var_dump ($generic->exists ('$id'));

// save() method

var_dump ($generic->save ());

// cascade() method

var_dump ($generic->cascade ('$id'));

// load() method

var_dump ($generic->load ('$app', '$pkg'));

?>
--EXPECT--
