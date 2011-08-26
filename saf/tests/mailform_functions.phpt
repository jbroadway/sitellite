--TEST--
saf.MailForm.Functions
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.MailForm.Functions');

// formdata_set() function

var_dump (formdata_set ('$name', '$list'));

// formrules_set() function

var_dump (formrules_set ('$name', '$list'));

// formdata_get() function

var_dump (formdata_get ('$list', '$makeAssoc'));

// formrules_get() function

var_dump (formrules_get ('$list', '$fieldName'));

?>
--EXPECT--
