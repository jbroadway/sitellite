--TEST--
saf.Session.Acl
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Session.Acl');

// constructor method

$sessionacl = new SessionAcl ('$user', '$role', '$team', '$teams');

// init() method

var_dump ($sessionacl->init ('$path'));

// initPrefs() method

var_dump ($sessionacl->initPrefs ());

// verify() method

var_dump ($sessionacl->verify ('$userDisabled'));

// allowed() method

var_dump ($sessionacl->allowed ('$resource', '$access', '$type'));

// _test() method

var_dump ($sessionacl->_test ('$check', '$value', '$all'));

// allowedSql() method

var_dump ($sessionacl->allowedSql ());

// approvedSql() method

var_dump ($sessionacl->approvedSql ());

// allowedAccessList() method

var_dump ($sessionacl->allowedAccessList ());

// allowedStatusList() method

var_dump ($sessionacl->allowedStatusList ());

// allowedTeamsList() method

var_dump ($sessionacl->allowedTeamsList ('$list'));

// isAdmin() method

var_dump ($sessionacl->isAdmin ());

// pref() method

var_dump ($sessionacl->pref ('$name'));

// prefSet() method

var_dump ($sessionacl->prefSet ('$name', '$value'));

// isResource() method

var_dump ($sessionacl->isResource ('$name'));

// adminRoles() method

var_dump ($sessionacl->adminRoles ());

// session_admin() function

var_dump (session_admin ());

// session_allowed() function

var_dump (session_allowed ('$resource', '$access', '$type'));

// session_allowed_sql() function

var_dump (session_allowed_sql ());

// session_approved_sql() function

var_dump (session_approved_sql ());

// session_allowed_access_list() function

var_dump (session_allowed_access_list ());

// session_allowed_status_list() function

var_dump (session_allowed_status_list ());

// session_allowed_teams_list() function

var_dump (session_allowed_teams_list ('$list'));

// session_pref() function

var_dump (session_pref ('$name'));

// session_pref_list() function

var_dump (session_pref_list ());

// session_pref_set() function

var_dump (session_pref_set ('$name', '$value'));

// session_get_statuses() function

var_dump (session_get_statuses ());

// session_get_access_levels() function

var_dump (session_get_access_levels ());

// session_get_resources() function

var_dump (session_get_resources ());

// session_get_teams() function

var_dump (session_get_teams ());

// session_get_roles() function

var_dump (session_get_roles ());

// session_role() function

var_dump (session_role ());

// session_team() function

var_dump (session_team ());

// session_is_resource() function

var_dump (session_is_resource ('$name'));

// session_admin_roles() function

var_dump (session_admin_roles ());

?>
--EXPECT--
