--TEST--
saf.CGI.ServerPush
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.CGI.ServerPush');

// constructor method

$serverpush = new ServerPush ('$type');

// send() method

var_dump ($serverpush->send ('$data', '$more', '$contentType'));

// end() method

var_dump ($serverpush->end ());

// rotate() method

var_dump ($serverpush->rotate ('$data', '$sleep'));

?>
--EXPECT--
