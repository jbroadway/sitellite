--TEST--
saf.Session.Manager
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Session.Manager');

// constructor method

$sessionmanager = new SessionManager;

// constructor method

$sessionmanager_user = new SessionManager_User;

// getList() method

var_dump ($sessionmanager_user->getList ('$offset', '$limit', '$order', '$ascdesc', '$role', '$team', '$name', '$disabled', '$public', '$teams'));

// add() method

var_dump ($sessionmanager_user->add ('$data'));

// edit() method

var_dump ($sessionmanager_user->edit ('$user', '$data'));

// delete() method

var_dump ($sessionmanager_user->delete ('$user'));

// getAddForm() method

var_dump ($sessionmanager_user->getAddForm ());

// getEditForm() method

var_dump ($sessionmanager_user->getEditForm ('$item'));

// constructor method

$sessionmanager_role = new SessionManager_Role;

// getData() method

var_dump ($sessionmanager_role->getData ());

// getList() method

var_dump ($sessionmanager_role->getList ());

// add() method

var_dump ($sessionmanager_role->add ('$name', '$data'));

// edit() method

var_dump ($sessionmanager_role->edit ('$name', '$newname', '$data'));

// delete() method

var_dump ($sessionmanager_role->delete ('$name'));

// getAddForm() method

var_dump ($sessionmanager_role->getAddForm ());

// getEditForm() method

var_dump ($sessionmanager_role->getEditForm ('$item'));

// constructor method

$sessionmanager_pref = new SessionManager_Pref;

// getData() method

var_dump ($sessionmanager_pref->getData ());

// getList() method

var_dump ($sessionmanager_pref->getList ());

// add() method

var_dump ($sessionmanager_pref->add ('$name', '$data'));

// edit() method

var_dump ($sessionmanager_pref->edit ('$name', '$newname', '$data'));

// delete() method

var_dump ($sessionmanager_pref->delete ('$name'));

// getAddForm() method

var_dump ($sessionmanager_pref->getAddForm ());

// getEditForm() method

var_dump ($sessionmanager_pref->getEditForm ('$item'));

// constructor method

$sessionmanager_team = new SessionManager_Team;

// getData() method

var_dump ($sessionmanager_team->getData ());

// getList() method

var_dump ($sessionmanager_team->getList ());

// add() method

var_dump ($sessionmanager_team->add ('$name', '$disabled', '$description'));

// edit() method

var_dump ($sessionmanager_team->edit ('$name', '$newname', '$disabled', '$description'));

// delete() method

var_dump ($sessionmanager_team->delete ('$name'));

// getAddForm() method

var_dump ($sessionmanager_team->getAddForm ());

// getEditForm() method

var_dump ($sessionmanager_team->getEditForm ('$item'));

// constructor method

$sessionmanager_simple = new SessionManager_Simple;

// getData() method

var_dump ($sessionmanager_simple->getData ());

// getList() method

var_dump ($sessionmanager_simple->getList ());

// add() method

var_dump ($sessionmanager_simple->add ('$name'));

// edit() method

var_dump ($sessionmanager_simple->edit ('$name', '$newname'));

// delete() method

var_dump ($sessionmanager_simple->delete ('$name'));

// constructor method

$sessionmanager_status = new SessionManager_Status;

// constructor method

$sessionmanager_access = new SessionManager_Access;

// constructor method

$sessionmanager_resource = new SessionManager_Resource;

?>
--EXPECT--
