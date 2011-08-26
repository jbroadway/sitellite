--TEST--
saf.GUI.Cell
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.GUI.Cell');

// constructor method

$htmlcell = new HtmlCell ('$template');

// set() method

var_dump ($htmlcell->set ('$property', '$value'));

// render() method

var_dump ($htmlcell->render ('$object', '$properties'));

?>
--EXPECT--
