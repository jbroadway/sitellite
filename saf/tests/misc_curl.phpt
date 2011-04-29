--TEST--
saf.Misc.Curl
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Misc.Curl');

// curl_init() function

var_dump (curl_init ());

// curl_setopt() function

var_dump (curl_setopt ('$ch', '$key', '$value'));

// curl_exec() function

var_dump (curl_exec ('$ch'));

// curl_error() function

var_dump (curl_error ('$ch'));

// curl_errno() function

var_dump (curl_errno ('$ch'));

// curl_close() function

var_dump (curl_close ('$ch'));

?>
--EXPECT--
