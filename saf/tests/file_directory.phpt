--TEST--
saf.File.Directory
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.File.Directory');

// constructor method

$dir = new Dir ('$dir_string');

// open() method

var_dump ($dir->open ('$dir_string'));

// readAll() method

var_dump ($dir->readAll ('$sorting_method'));

// read_all() method

var_dump ($dir->read_all ('$sorting_method'));

// read() method

var_dump ($dir->read ());

// rewind() method

var_dump ($dir->rewind ());

// close() method

var_dump ($dir->close ());

// _sort() method

var_dump ($dir->_sort ('$method'));

// matches() method

var_dump ($dir->matches ('$pattern', '$file'));

// find() method

var_dump ($dir->find ('$pattern', '$basedir', '$recursive'));

// findInFiles() method

var_dump ($dir->findInFiles ('$string', '$basedir', '$recursive', '$regex'));

// find_in_files() method

var_dump ($dir->find_in_files ('$string', '$basedir', '$recursive', '$regex'));

// build() method

var_dump ($dir->build ('$path', '$mode'));

// rmdirRecursive() method

var_dump ($dir->rmdirRecursive ('$path'));

// rmdir_recursive() method

var_dump ($dir->rmdir_recursive ('$path'));

// fetch() method

var_dump ($dir->fetch ('$path', '$skipDots'));

// getStruct() method

var_dump ($dir->getStruct ('$path'));

?>
--EXPECT--
