--TEST--
saf.XML.RelaxNG.Parser
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.RelaxNG.Parser');

// constructor method

$rngparser = new RNGParser;

// parse() method

var_dump ($rngparser->parse ('$schema'));

// makeMethod() method

var_dump ($rngparser->makeMethod ('$tag', '$type'));

// _default() method

var_dump ($rngparser->_default ('$node'));

// _element() method

var_dump ($rngparser->_element ('$node'));

// _close_element() method

var_dump ($rngparser->_close_element ('$node'));

// _attribute() method

var_dump ($rngparser->_attribute ('$node'));

// _close_attribute() method

var_dump ($rngparser->_close_attribute ('$node'));

// _zeroOrMore() method

var_dump ($rngparser->_zeroOrMore ('$node'));

// _oneOrMore() method

var_dump ($rngparser->_oneOrMore ('$node'));

// _optional() method

var_dump ($rngparser->_optional ('$node'));

// _ref() method

var_dump ($rngparser->_ref ('$node'));

// _type() method

var_dump ($rngparser->_type ('$node'));

?>
--EXPECT--
