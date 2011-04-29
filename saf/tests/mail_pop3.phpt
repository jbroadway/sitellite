--TEST--
saf.Mail.Pop3
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Mail.Pop3');

// constructor method

$pop3 = new Pop3 ('$server', '$port', '$timeout');

// connect() method

var_dump ($pop3->connect ());

// authenticate() method

var_dump ($pop3->authenticate ('$user', '$pass'));

// listMessages() method

var_dump ($pop3->listMessages ());

// getMessage() method

var_dump ($pop3->getMessage ('$number'));

// getTop() method

var_dump ($pop3->getTop ('$number', '$lines'));

// uniqueID() method

var_dump ($pop3->uniqueID ('$number'));

// removeMessage() method

var_dump ($pop3->removeMessage ('$number'));

// stat() method

var_dump ($pop3->stat ());

// reset() method

var_dump ($pop3->reset ());

// noop() method

var_dump ($pop3->noop ());

// disconnect() method

var_dump ($pop3->disconnect ());

// getResponse() method

var_dump ($pop3->getResponse ('$oneliner'));

// parseResponse() method

var_dump ($pop3->parseResponse ('$resp'));

?>
--EXPECT--
