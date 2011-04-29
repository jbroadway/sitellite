--TEST--
saf.Parser.Tokenizer
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Parser.Tokenizer');

// constructor method

$tokenizer = new Tokenizer;

// normalize() method

var_dump ($tokenizer->normalize ('$tokens'));

// parse() method

var_dump ($tokenizer->parse ('$code'));

// addCallback() method

var_dump ($tokenizer->addCallback ('$function', '$token'));

// _default() method

var_dump ($tokenizer->_default ('$token', '$data'));

?>
--EXPECT--
