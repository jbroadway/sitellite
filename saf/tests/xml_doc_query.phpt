--TEST--
saf.XML.Doc.Query
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.Doc.Query');

// constructor method

$xmldocquery = new XMLDocQuery;

// query() method

var_dump ($xmldocquery->query ('$rootNode', '$query'));

// queryFile() method

var_dump ($xmldocquery->queryFile ('$file', '$query'));

// evalCond() method

var_dump ($xmldocquery->evalCond ('$op', '$one', '$two'));

// evaluate() method

var_dump ($xmldocquery->evaluate ('$node', '$struct'));

// getNodes() method

var_dump ($xmldocquery->getNodes ('$nodes', '$name', '$recursive'));

// _default() method

var_dump ($xmldocquery->_default ('$token', '$name'));

// _condition() method

var_dump ($xmldocquery->_condition ('$token', '$name'));

// _literal() method

var_dump ($xmldocquery->_literal ('$token', '$name'));

// _attribute() method

var_dump ($xmldocquery->_attribute ('$token', '$name', '$regs'));

// _nodeNum() method

var_dump ($xmldocquery->_nodeNum ('$token', '$name'));

// _nodeAttr() method

var_dump ($xmldocquery->_nodeAttr ('$token', '$name'));

// _node() method

var_dump ($xmldocquery->_node ('$token', '$name'));

?>
--EXPECT--
