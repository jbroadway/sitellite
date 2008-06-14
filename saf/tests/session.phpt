--TEST--
saf.Session
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Session');

// constructor method

$session = new Session ('$handler', '$sources', '$store', '$username', '$password', '$id');

// init() method

var_dump ($session->init ('$path'));

// setTimeout() method

var_dump ($session->setTimeout ('$timeout'));

// setSourceProperties() method

var_dump ($session->setSourceProperties ('$source', '$properties'));

// setHandlerProperties() method

var_dump ($session->setHandlerProperties ('$properties'));

// setStoreProperties() method

var_dump ($session->setStoreProperties ('$properties'));

// authorize() method

var_dump ($session->authorize ('$user', '$pass', '$id'));

// start() method

var_dump ($session->start ());

// sendAuthRequest() method

var_dump ($session->sendAuthRequest ());

// gatherParameters() method

var_dump ($session->gatherParameters ('$handler', '$sessionidname'));

// makePendingKey() method

var_dump ($session->makePendingKey ());

// close() method

var_dump ($session->close ());

// get() method

var_dump ($session->get ('$name'));

// set() method

var_dump ($session->set ('$name', '$value'));

// append() method

var_dump ($session->append ('$name', '$value'));

// save() method

var_dump ($session->save ());

// allowed() method

var_dump ($session->allowed ('$resource', '$access', '$type'));

// allowedSql() method

var_dump ($session->allowedSql ());

// makeRecoverKey() method

var_dump ($session->makeRecoverKey ());

// isValidKey() method

var_dump ($session->isValidKey ('$user', '$key'));

// update() method

var_dump ($session->update ('$data'));

// getUser() method

var_dump ($session->getUser ('$username'));

// getUserByEmail() method

var_dump ($session->getUserByEmail ('$email'));

// getManager() method

var_dump ($session->getManager ());

// session_username() function

var_dump (session_username ());

// session_password() function

var_dump (session_password ());

// session_valid() function

var_dump (session_valid ());

// session_get() function

var_dump (session_get ('$name'));

// session_set() function

var_dump (session_set ('$name', '$value'));

// session_append() function

var_dump (session_append ('$name', '$value'));

// session_save() function

var_dump (session_save ());

// session_get_manager() function

var_dump (session_get_manager ());

// session_get_user() function

var_dump (session_get_user ('$user'));

// session_make_pending_key() function

var_dump (session_make_pending_key ());

// session_is_valid_key() function

var_dump (session_is_valid_key ('$user', '$key'));

// session_authorize() function

var_dump (session_authorize ('$user', '$pass', '$id'));

?>
--EXPECT--
