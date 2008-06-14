--TEST--
saf.Database.LDAP
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Database.LDAP');

// constructor method

$ldap = new LDAP ('$server', '$port', '$rdn', '$password', '$secure');

// connect() method

var_dump ($ldap->connect ('$server', '$port', '$secure'));

// bind() method

var_dump ($ldap->bind ('$rdn', '$password'));

// search() method

var_dump ($ldap->search ('$dn', '$filter', '$attrs'));

// getEntries() method

var_dump ($ldap->getEntries ());

// fetch() method

var_dump ($ldap->fetch ());

// makeObj() method

var_dump ($ldap->makeObj ('$attrs'));

// free() method

var_dump ($ldap->free ());

// close() method

var_dump ($ldap->close ());

?>
--EXPECT--
