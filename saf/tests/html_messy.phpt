--TEST--
saf.HTML.Messy
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.HTML.Messy');

// constructor method

$messy = new Messy;

// parse() method

var_dump ($messy->parse ('$data', '$isXml'));

// handle_data() method

var_dump ($messy->handle_data ('$parser', '$data'));

// handle_comment() method

var_dump ($messy->handle_comment ('$parser', '$data'));

// handle_doctype() method

var_dump ($messy->handle_doctype ('$parser', '$data'));

// handle_start_tag() method

var_dump ($messy->handle_start_tag ('$parser', '$tag', '$attrs'));

// handle_end_tag() method

var_dump ($messy->handle_end_tag ('$parser', '$tag'));

// pad() method

var_dump ($messy->pad ('$length'));

// toXML() method

var_dump ($messy->toXML ());

// clean() method

var_dump ($messy->clean ('$doc', '$isXml'));

// toXMLDoc() method

var_dump ($messy->toXMLDoc ());

?>
--EXPECT--
