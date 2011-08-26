--TEST--
saf.Template.Transformation
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Template.Transformation');

// constructor method

$templatetransformation = new TemplateTransformation ('$type', '$key', '$rule');

// transform() method

var_dump ($templatetransformation->transform ('$value', '$import'));

// parse() method

var_dump ($templatetransformation->parse ('$data'));

?>
--EXPECT--
