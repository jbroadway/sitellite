--TEST--
saf.GUI.Layout
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.GUI.Layout');

// constructor method

$htmllayout = new HtmlLayout ('$x', '$y');

// assign() method

var_dump ($htmllayout->assign ('$part', '$template'));

// append() method

var_dump ($htmllayout->append ('$part', '$template'));

// addCol() method

var_dump ($htmllayout->addCol ());

// addRow() method

var_dump ($htmllayout->addRow ());

// set() method

var_dump ($htmllayout->set ('$part', '$property', '$value'));

// col() method

var_dump ($htmllayout->col ('$num'));

// row() method

var_dump ($htmllayout->row ('$num'));

// walk() method

var_dump ($htmllayout->walk ());

// sub() method

var_dump ($htmllayout->sub ('$x', '$xx', '$yy'));

// render() method

var_dump ($htmllayout->render ('$td'));

// spanRows() method

var_dump ($htmllayout->spanRows ('$part', '$num'));

// spanCols() method

var_dump ($htmllayout->spanCols ('$part', '$num'));

// translate() method

var_dump ($htmllayout->translate ('$x', '$y'));

?>
--EXPECT--
