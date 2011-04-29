--TEST--
saf.Database.Query
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Database.Query');

// constructor method

$query = new Query ('$sql', '$connection', '$cache');

// bind_values() method

var_dump ($query->bind_values ('$values'));

// execute() method

var_dump ($query->execute ());

// field() method

var_dump ($query->field ('$num'));

// rows() method

var_dump ($query->rows ());

// lastid() method

var_dump ($query->lastid ());

// fetch() method

var_dump ($query->fetch ());

// toXML() method

var_dump ($query->toXML ('$data_obj', '$root_node'));

// fetchXML() method

var_dump ($query->fetchXML ());

// fetchArray() method

var_dump ($query->fetchArray ());

// free() method

var_dump ($query->free ());

// errno() method

var_dump ($query->errno ());

// error() method

var_dump ($query->error ());

?>
--EXPECT--
