--TEST--
saf.Template.Simple
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Template.Simple');

// constructor method

$simpletemplate = new SimpleTemplate ('$path', '$use_delim');

// setDelim() method

var_dump ($simpletemplate->setDelim ('$use_delim'));

// getPath() method

var_dump ($simpletemplate->getPath ());

// determine() method

var_dump ($simpletemplate->determine ('$var', '$obj'));

// fill() method

var_dump ($simpletemplate->fill ('$tpl', '$obj'));

// _fill() method

var_dump ($simpletemplate->_fill ('$tpl', '$obj'));

// register() method

var_dump ($simpletemplate->register ('$name', '$var'));

// template_simple() function

var_dump (template_simple ('$tpl', '$obj'));

// template_simple_register() function

var_dump (template_simple_register ('$name', '$var'));

?>
--EXPECT--
