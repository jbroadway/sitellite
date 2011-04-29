--TEST--
saf.Diff
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Diff');

// constructor method

$diff = new Diff ('$splitMode');

// compare() method

var_dump ($diff->compare ('$str1', '$str2'));

// format() method

var_dump ($diff->format ('$a', '$r', '$i'));

?>
--EXPECT--
