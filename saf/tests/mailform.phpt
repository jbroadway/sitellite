--TEST--
saf.MailForm
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm');

// constructor method

$mailform = new MailForm ('$action', '$method');

// addWidget() method

var_dump ($mailform->addWidget ('$type', '$name'));

// invalid() method

var_dump ($mailform->invalid ());

// setValues() method

var_dump ($mailform->setValues ('$cgi', '$invalid'));

// show() method

var_dump ($mailform->show ('$template'));

// getValues() method

var_dump ($mailform->getValues ());

// setHandler() method

var_dump ($mailform->setHandler ('$func'));

// run() method

var_dump ($mailform->run ('$uploadFiles'));

// onSubmit() method

var_dump ($mailform->onSubmit ('$vals'));

// handle() method

var_dump ($mailform->handle ('$email', '$subject', '$from_field', '$extra'));

// formatEmail() method

var_dump ($mailform->formatEmail ('$vals'));

// parseSettings() method

var_dump ($mailform->parseSettings ('$file'));

// createWidget() method

var_dump ($mailform->createWidget ('$name', '$data'));

// makeAssoc() method

var_dump ($mailform->makeAssoc ('$list'));

// rememberFields() method

var_dump ($mailform->rememberFields ('$list'));

// attr() method

var_dump ($mailform->attr ('$key', '$value'));

// unsetAttr() method

var_dump ($mailform->unsetAttr ('$key'));

// getAttrs() method

var_dump ($mailform->getAttrs ());

// verifyRequestMethod() method

var_dump ($mailform->verifyRequestMethod ());

// verifyReferer() method

var_dump ($mailform->verifyReferer ());

?>
--EXPECT--
