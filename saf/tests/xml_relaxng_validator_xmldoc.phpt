--TEST--
saf.XML.RelaxNG.Validator.XMLDoc
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.RelaxNG.Validator.XMLDoc');

// constructor method

$rngvalidator_xmldoc = new RNGValidator_XMLDoc;

// validate() method

var_dump ($rngvalidator_xmldoc->validate ('$doc', '$schema'));

// _evaluate() method

var_dump ($rngvalidator_xmldoc->_evaluate ('$node', '$level'));

?>
--EXPECT--
