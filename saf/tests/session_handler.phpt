--TEST--
saf.Session.Handler
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Session.Handler');

// constructor method

$sessionhandler = new SessionHandler;

// setProperties() method

var_dump ($sessionhandler->setProperties ('$properties'));

// start() method

var_dump ($sessionhandler->start ('$id', '$authorized'));

// sendAuthRequest() method

var_dump ($sessionhandler->sendAuthRequest ());

// close() method

var_dump ($sessionhandler->close ());

// changeTimeout() method

var_dump ($sessionhandler->changeTimeout ('$newduration'));

// session_change_timeout() function

var_dump (session_change_timeout ('$newduration'));

?>
--EXPECT--
