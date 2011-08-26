--TEST--
saf.CGI.IOFilter
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.CGI.IOFilter');

// constructor method

$iofilter = new IOFilter ('$name', '$contentType');

// addHeader() method

var_dump ($iofilter->addHeader ('$header'));

// transform() method

var_dump ($iofilter->transform ('$content'));

// sendHeaders() method

var_dump ($iofilter->sendHeaders ());

?>
--EXPECT--
