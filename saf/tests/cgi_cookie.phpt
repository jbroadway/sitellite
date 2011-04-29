--TEST--
saf.CGI.Cookie
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.CGI.Cookie');

// constructor method

$cookie = new Cookie;

// prependDot() method

var_dump ($cookie->prependDot ('$domain'));

// set() method

var_dump ($cookie->set ('$name', '$value', '$expire', '$path', '$domain', '$secure'));

?>
--EXPECT--
