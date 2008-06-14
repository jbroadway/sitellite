--TEST--
saf.Database.Table
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Database.Table');

// constructor method

$databasetable = new DatabaseTable ('$db', '$name', '$pkey');

// fetch() method

var_dump ($databasetable->fetch ('$keyval', '$columns', '$order', '$ascdesc'));

// fetchAll() method

var_dump ($databasetable->fetchAll ('$columns', '$order', '$ascdesc'));

// insert() method

var_dump ($databasetable->insert ('$columns'));

// update() method

var_dump ($databasetable->update ('$keyval', '$columns'));

// delete() method

var_dump ($databasetable->delete ('$keyval'));

// getPkey() method

var_dump ($databasetable->getPkey ());

// getInfo() method

var_dump ($databasetable->getInfo ());

// getRefInfo() method

var_dump ($databasetable->getRefInfo ('$name', '$table'));

// _makeWidget() method

var_dump ($databasetable->_makeWidget ('$col'));

// changeType() method

var_dump ($databasetable->changeType ('$name', '$type', '$extra'));

// addFacet() method

var_dump ($databasetable->addFacet ('$column', '$title', '$extra'));

?>
--EXPECT--
