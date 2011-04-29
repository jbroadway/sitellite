--TEST--
saf.Database.SQL
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Database.SQL');

// constructor method

$sql = new SQL ('$driver', '$path', '$page');

// abstractSql() method

var_dump ($sql->abstractSql ('$sql'));

?>
--EXPECT--
