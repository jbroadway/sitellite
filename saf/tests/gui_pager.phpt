--TEST--
saf.GUI.Pager
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.GUI.Pager');

// constructor method

$pager = new Pager ('$offset', '$limit', '$total', '$maxPages');

// _translate() method

var_dump ($pager->_translate ());

// getInfo() method

var_dump ($pager->getInfo ());

// update() method

var_dump ($pager->update ());

// getPages() method

var_dump ($pager->getPages ('$max'));

// query() method

var_dump ($pager->query ('$sql', '$bind'));

// setData() method

var_dump ($pager->setData ('$data'));

// _uenc() method

var_dump ($pager->_uenc ('$list'));

// setUrl() method

var_dump ($pager->setUrl ('$url'));

?>
--EXPECT--
