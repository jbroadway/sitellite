--TEST--
saf.Session.Source.Database
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Session.Source.Database');

// constructor method

$sessionsource_database = new SessionSource_Database;

// authorize() method

var_dump ($sessionsource_database->authorize ('$username', '$password', '$id'));

// close() method

var_dump ($sessionsource_database->close ());

// getRole() method

var_dump ($sessionsource_database->getRole ());

// getTeam() method

var_dump ($sessionsource_database->getTeam ());

// getTeams() method

var_dump ($sessionsource_database->getTeams ());

// isDisabled() method

var_dump ($sessionsource_database->isDisabled ());

// getUser() method

var_dump ($sessionsource_database->getUser ('$user'));

// getUserByEmail() method

var_dump ($sessionsource_database->getUserByEmail ('$email'));

// isValidKey() method

var_dump ($sessionsource_database->isValidKey ('$user', '$key'));

// add() method

var_dump ($sessionsource_database->add ('$data'));

// _add() method

var_dump ($sessionsource_database->_add ('$data'));

// update() method

var_dump ($sessionsource_database->update ('$data', '$user'));

// _update() method

var_dump ($sessionsource_database->_update ('$data'));

// delete() method

var_dump ($sessionsource_database->delete ('$user'));

// getTotal() method

var_dump ($sessionsource_database->getTotal ('$role', '$team', '$public'));

// getActive() method

var_dump ($sessionsource_database->getActive ());

// getList() method

var_dump ($sessionsource_database->getList ('$offset', '$limit', '$order', '$ascdesc', '$role', '$team', '$name', '$disabled', '$public', '$teams'));

?>
--EXPECT--
