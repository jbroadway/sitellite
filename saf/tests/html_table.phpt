--TEST--
saf.HTML.Table
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.HTML.Table');

// constructor method

$htmltable = new HtmlTable ('$data');

// setData() method

var_dump ($htmltable->setData ('$data'));

// firstRowHeaders() method

var_dump ($htmltable->firstRowHeaders ('$frh'));

// setWidths() method

var_dump ($htmltable->setWidths ('$widths'));

// render() method

var_dump ($htmltable->render ('$data', '$frh'));

?>
--EXPECT--
