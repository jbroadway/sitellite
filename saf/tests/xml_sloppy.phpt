--TEST--
saf.XML.Sloppy
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.Sloppy');

// constructor method

$sloppydom = new SloppyDOM ('$encoding');

// parseFromFile() method

var_dump ($sloppydom->parseFromFile ('$filename', '$cacheFile'));

// parse() method

var_dump ($sloppydom->parse ('$data'));

// tag_open() method

var_dump ($sloppydom->tag_open ('$parser', '$tag', '$attributes'));

// cdata() method

var_dump ($sloppydom->cdata ('$parser', '$cdata'));

// tag_close() method

var_dump ($sloppydom->tag_close ('$parser', '$tag'));

// _default() method

var_dump ($sloppydom->_default ('$parser', '$data'));

?>
--EXPECT--
