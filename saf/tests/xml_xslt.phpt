--TEST--
saf.XML.XSLT
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.XSLT');

// constructor method

$xslt = new XSLT;

// process() method

var_dump ($xslt->process ('$xsl_data', '$xml_data', '$params'));

// error() method

var_dump ($xslt->error ());

// errno() method

var_dump ($xslt->errno ());

// free() method

var_dump ($xslt->free ());

?>
--EXPECT--
