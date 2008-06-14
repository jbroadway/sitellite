--TEST--
saf.Session.Source
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Session.Source');

// constructor method

$sessionsource = new SessionSource;

// setProperties() method

var_dump ($sessionsource->setProperties ('$properties'));

// authorize() method

var_dump ($sessionsource->authorize ('$username', '$password', '$id'));

// close() method

var_dump ($sessionsource->close ());

// getRole() method

var_dump ($sessionsource->getRole ());

// getTeam() method

var_dump ($sessionsource->getTeam ());

// getTeams() method

var_dump ($sessionsource->getTeams ());

// isDisabled() method

var_dump ($sessionsource->isDisabled ());

// getUser() method

var_dump ($sessionsource->getUser ('$user'));

// getUserByEmail() method

var_dump ($sessionsource->getUserByEmail ('$email'));

// isValidKey() method

var_dump ($sessionsource->isValidKey ('$user', '$key'));

// add() method

var_dump ($sessionsource->add ('$data'));

// update() method

var_dump ($sessionsource->update ('$data', '$user'));

// delete() method

var_dump ($sessionsource->delete ('$user'));

// getTotal() method

var_dump ($sessionsource->getTotal ('$role', '$team', '$public'));

// getActive() method

var_dump ($sessionsource->getActive ());

// getList() method

var_dump ($sessionsource->getList ('$offset', '$limit', '$order', '$ascdesc', '$role', '$team', '$name'));

// session_user_get() function

var_dump (session_user_get ('$user'));

// session_user_get_list() function

var_dump (session_user_get_list ('$offset', '$limit', '$order', '$ascdesc', '$role', '$team', '$name', '$disabled', '$public', '$teams'));

// session_user_get_total() function

var_dump (session_user_get_total ('$role', '$team', '$public'));

// session_user_get_active() function

var_dump (session_user_get_active ());

// session_user_add() function

var_dump (session_user_add ('$data'));

// session_user_edit() function

var_dump (session_user_edit ('$user', '$data'));

// session_user_delete() function

var_dump (session_user_delete ('$user'));

// session_user_total() function

var_dump (session_user_total ());

// session_user_error() function

var_dump (session_user_error ());

// session_user_is_unique() function

var_dump (session_user_is_unique ('$user'));

// session_user_get_email() function

var_dump (session_user_get_email ('$user'));

?>
--EXPECT--
