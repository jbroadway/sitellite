--TEST--
saf.Misc.RPC
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Misc.RPC');

// rpc_init() function

var_dump (rpc_init ('$handler', '$render'));

// rpc_response() function

var_dump (rpc_response ());

// rpc_serialize() function

var_dump (rpc_serialize ('$val'));

// rpc_handle() function

var_dump (rpc_handle ('$obj', '$parameters'));

?>
--EXPECT--
