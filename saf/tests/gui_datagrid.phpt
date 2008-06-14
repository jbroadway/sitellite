--TEST--
saf.GUI.DataGrid
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.GUI.DataGrid');

// constructor method

$datagrid = new DataGrid ('$collection', '$fields', '$limit');

// primaryKey() method

var_dump ($datagrid->primaryKey ('$field'));

// setEditUrl() method

var_dump ($datagrid->setEditUrl ('$url'));

// setAddUrl() method

var_dump ($datagrid->setAddUrl ('$url'));

// setDeleteUrl() method

var_dump ($datagrid->setDeleteUrl ('$url'));

// skipHeader() method

var_dump ($datagrid->skipHeader ('$name'));

// rememberValue() method

var_dump ($datagrid->rememberValue ('$name', '$value'));

// rememberParams() method

var_dump ($datagrid->rememberParams ());

// rememberHiddenValues() method

var_dump ($datagrid->rememberHiddenValues ());

// filter() method

var_dump ($datagrid->filter ('$name', '$func'));

// getList() method

var_dump ($datagrid->getList ());

// draw() method

var_dump ($datagrid->draw ());

// datagrid_filter() function

var_dump (datagrid_filter ('$val'));

?>
--EXPECT--
