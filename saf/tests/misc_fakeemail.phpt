--TEST--
saf.Misc.FakeEmail
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Misc.FakeEmail');

// constructor method

$fakeemail = new FakeEmail;

// makeWords() method

var_dump ($fakeemail->makeWords ('$addy'));

// insertComments() method

var_dump ($fakeemail->insertComments ('$addy'));

// obfuscateLink() method

var_dump ($fakeemail->obfuscateLink ('$addy'));

// doAll() method

var_dump ($fakeemail->doAll ('$addy'));

?>
--EXPECT--
