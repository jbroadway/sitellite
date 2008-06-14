--TEST--
saf.XML.XT.Expression
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.XT.Expression');

// constructor method

$xtexpression = new XTExpression ('$object');

// setObject() method

var_dump ($xtexpression->setObject ('$obj', '$name'));

// register() method

var_dump ($xtexpression->register ('$name'));

// resetRegister() method

var_dump ($xtexpression->resetRegister ());

// getObject() method

var_dump ($xtexpression->getObject ('$name'));

// setCurrent() method

var_dump ($xtexpression->setCurrent ('$obj', '$name', '$index', '$total'));

// splitAssignment() method

var_dump ($xtexpression->splitAssignment ('$string'));

// splitStatement() method

var_dump ($xtexpression->splitStatement ('$string'));

// getPath() method

var_dump ($xtexpression->getPath ('$path'));

// define() method

var_dump ($xtexpression->define ('$string', '$node', '$default_type'));

// defineObject() method

var_dump ($xtexpression->defineObject ('$name', '$evalstr'));

// defineValue() method

var_dump ($xtexpression->defineValue ('$name', '$value'));

// repeat() method

var_dump ($xtexpression->repeat ('$string', '$node', '$default_type'));

// evaluate() method

var_dump ($xtexpression->evaluate ('$string', '$node', '$default_type', '$carry'));

// _innerPath() method

var_dump ($xtexpression->_innerPath ('$token', '$name'));

// _endInnerPath() method

var_dump ($xtexpression->_endInnerPath ('$token', '$name'));

// _phpOpen() method

var_dump ($xtexpression->_phpOpen ('$token', '$name'));

// _stringOpen() method

var_dump ($xtexpression->_stringOpen ('$token', '$name'));

// _pathOpen() method

var_dump ($xtexpression->_pathOpen ('$token', '$name'));

// _else() method

var_dump ($xtexpression->_else ('$token', '$name'));

// _escape() method

var_dump ($xtexpression->_escape ('$token', '$name'));

// _default() method

var_dump ($xtexpression->_default ('$token', '$name'));

?>
--EXPECT--
