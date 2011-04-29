--TEST--
saf.Cache
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Cache');

// constructor method

$cache = new Cache ('$dir');

// setIgnoreChars() method

var_dump ($cache->setIgnoreChars ('$ignore'));

// serialize() method

var_dump ($cache->serialize ('$file'));

// file() method

var_dump ($cache->file ('$file', '$data'));

// expired() method

var_dump ($cache->expired ('$file', '$duration'));

// expire() method

var_dump ($cache->expire ('$file'));

// show() method

var_dump ($cache->show ('$file'));

// is_cacheable() method

var_dump ($cache->is_cacheable ('$uri', '$list'));

// shutdown() method

var_dump ($cache->shutdown ());

// clear() method

var_dump ($cache->clear ());

?>
--EXPECT--
