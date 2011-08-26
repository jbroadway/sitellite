--TEST--
saf.Parser
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Parser');

// constructor method

$parser = new Parser;

// addInternal() method

var_dump ($parser->addInternal ('$name', '$token', '$quote'));

// addToken() method

var_dump ($parser->addToken ('$name', '$token', '$quote'));

// makeRegex() method

var_dump ($parser->makeRegex ());

// parse() method

var_dump ($parser->parse ('$data'));

// _default() method

var_dump ($parser->_default ('$token', '$name'));

?>
--EXPECT--
