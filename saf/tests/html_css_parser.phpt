--TEST--
saf.HTML.CSS_Parser
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.HTML.CSS_Parser');

// constructor method

$css_parser = new CSS_Parser;

// getClasses() method

var_dump ($css_parser->getClasses ('$tag'));

// getIDs() method

var_dump ($css_parser->getIDs ('$tag'));

// getStyle() method

var_dump ($css_parser->getStyle ('$tag'));

// _comment() method

var_dump ($css_parser->_comment ('$token', '$name'));

// _comment_end() method

var_dump ($css_parser->_comment_end ('$token', '$name'));

// _default() method

var_dump ($css_parser->_default ('$token', '$name'));

// _block() method

var_dump ($css_parser->_block ('$token', '$name'));

// _block_end() method

var_dump ($css_parser->_block_end ('$token', '$name'));

?>
--EXPECT--
