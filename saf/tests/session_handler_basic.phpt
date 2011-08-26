--TEST--
saf.Session.Handler.Basic
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Session.Handler.Basic');

// constructor method

$sessionhandler_basic = new SessionHandler_Basic;

// start() method

var_dump ($sessionhandler_basic->start ('$id', '$authorized'));

// sendAuthRequest() method

var_dump ($sessionhandler_basic->sendAuthRequest ());

// gatherParameters() method

var_dump ($sessionhandler_basic->gatherParameters ('$sessionidname'));

?>
--EXPECT--
