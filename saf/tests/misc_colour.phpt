--TEST--
saf.Misc.Colour
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Misc.Colour');

// colour_diff() function

var_dump (colour_diff ('$c1', '$c2', '$percent'));

// colour_gradient() function

var_dump (colour_gradient ('$c1', '$c2', '$divisions'));

?>
--EXPECT--
