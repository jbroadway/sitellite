--TEST--
saf.MailForm.Rule
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Rule');

// constructor method

$mailformrule = new MailFormRule ('$rule', '$name', '$msg');

// parseRuleStatement() method

var_dump ($mailformrule->parseRuleStatement ('$rule'));

// validate() method

var_dump ($mailformrule->validate ('$value', '$form', '$cgi'));

// _validate() method

var_dump ($mailformrule->_validate ('$value', '$form', '$cgi'));

// _validateNegated() method

var_dump ($mailformrule->_validateNegated ('$value', '$form', '$cgi'));

?>
--EXPECT--
