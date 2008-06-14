--TEST--
saf.Misc.Microbench
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Misc.Microbench');

// microbench() function

var_dump (microbench ('$tag', '$calculate'));

// microbench_display() function

var_dump (microbench_display ());

?>
--EXPECT--
