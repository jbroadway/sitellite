--TEST--
saf.CGI.UploadedFile
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.CGI.UploadedFile');

// constructor method

$uploadedfile = new UploadedFile ('$hash');

// move() method

var_dump ($uploadedfile->move ('$to_path', '$to_file'));

// get() method

var_dump ($uploadedfile->get ());

?>
--EXPECT--
