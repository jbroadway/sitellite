--TEST--
saf.Misc.TableHeader
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Misc.TableHeader');

// constructor method

$tableheader = new TableHeader ('$name', '$fullname');

// isCurrent() method

var_dump ($tableheader->isCurrent ());

// getSort() method

var_dump ($tableheader->getSort ());

?>
--EXPECT--
