--TEST--
saf.Database.Facet
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Database.Facet');

// constructor method

$databasefacet = new DatabaseFacet ('$tableObj', '$column', '$title', '$extra');

// compile() method

var_dump ($databasefacet->compile ());

// addItem() method

var_dump ($databasefacet->addItem ('$id', '$title', '$count'));

// show() method

var_dump ($databasefacet->show ('$linkUrl'));

?>
--EXPECT--
