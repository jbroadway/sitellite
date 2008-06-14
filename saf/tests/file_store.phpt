--TEST--
saf.File.Store
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.File.Store');

// constructor method

$filestore = new FileStore ('$path', '$ignoreChars');

// init() method

var_dump ($filestore->init ());

// buildDirs() method

var_dump ($filestore->buildDirs ('$dir', '$depth'));

// initDir() method

var_dump ($filestore->initDir ('$path', '$dirs'));

// getPath() method

var_dump ($filestore->getPath ('$file'));

// exists() method

var_dump ($filestore->exists ('$file'));

// open() method

var_dump ($filestore->open ('$file', '$mode'));

// get() method

var_dump ($filestore->get ('$file', '$length'));

// put() method

var_dump ($filestore->put ('$file', '$data', '$length'));

// append() method

var_dump ($filestore->append ('$file', '$data', '$length'));

// move() method

var_dump ($filestore->move ('$file', '$oldFile', '$isUploaded'));

// copy() method

var_dump ($filestore->copy ('$file', '$oldFile'));

// remove() method

var_dump ($filestore->remove ('$file'));

// listAll() method

var_dump ($filestore->listAll ('$prefix', '$limit', '$offset'));

// countAll() method

var_dump ($filestore->countAll ('$prefix'));

?>
--EXPECT--
