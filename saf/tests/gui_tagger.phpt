--TEST--
saf.GUI.Tagger
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.GUI.Tagger');

// constructor method

$tagger = new Tagger ('$data_table', '$tag_table');

// fetch() method

var_dump ($tagger->fetch ());

// getLevel() method

var_dump ($tagger->getLevel ('$n', '$b', '$c'));

// display() method

var_dump ($tagger->display ('$url'));

?>
--EXPECT--
