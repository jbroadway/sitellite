--TEST--
saf.Session.Handler.Cookie
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Session.Handler.Cookie');

// constructor method

$sessionhandler_cookie = new SessionHandler_Cookie;

// start() method

var_dump ($sessionhandler_cookie->start ('$id', '$authorized'));

// changeTimeout() method

var_dump ($sessionhandler_cookie->changeTimeout ('$newduration'));

// sendAuthRequest() method

var_dump ($sessionhandler_cookie->sendAuthRequest ());

// gatherParameters() method

var_dump ($sessionhandler_cookie->gatherParameters ('$sessionidname'));

// close() method

var_dump ($sessionhandler_cookie->close ());

?>
--EXPECT--
