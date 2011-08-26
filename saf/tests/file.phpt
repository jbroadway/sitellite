--TEST--
saf.File
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.File');

// constructor method

$file = new File ('$abs_file_path', '$www_file_path');

// format_filesize() method

var_dump ($file->format_filesize ('$size'));

// get_mime() method

var_dump ($file->get_mime ('$db'));

// contents() method

var_dump ($file->contents ('$file'));

// contains() method

var_dump ($file->contains ('$string', '$regex', '$file', '$count', '$filter'));

// formatPerms() method

var_dump ($file->formatPerms ('$mode'));

// overwrite() method

var_dump ($file->overwrite ('$data', '$file'));

// append() method

var_dump ($file->append ('$data', '$file'));

// rand() method

var_dump ($file->rand ('$path', '$ext'));

// determine() method

var_dump ($file->determine ('$list', '$path'));

// file_overwrite() function

var_dump (file_overwrite ('$file', '$data'));

// file_append() function

var_dump (file_append ('$file', '$data'));

// file_rand() function

var_dump (file_rand ('$path', '$ext'));

// file_determine() function

var_dump (file_determine ('$list', '$path'));

?>
--EXPECT--
