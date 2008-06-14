--TEST--
saf.Loader
--FILE--
<?php

function phpt_replace_abs_path ($out) {
	$cwd = getcwd ();
	chdir ('../..');
	return str_replace (getcwd (), '', $out);
	chdir ($cwd);
}

ob_start ('phpt_replace_abs_path');

include_once ('../init.php');

// 1. basic parameter parsing (constructor and object state)

var_dump ($loader);

// 2. import()

var_dump ($loader->import ('saf.Date'));

var_dump (class_exists ('Date'));

var_dump ($loader->import ('saf.FakePackage'));

// 3. addPath()

var_dump ($loader->addPath ('...'));
var_dump ($loader->addPath (array ('myapp' => 'inc/app/myapp')));
var_dump ($loader->paths['myapp']);
var_dump ($loader->path ());

// 4. find()

var_dump ($loader->find ('MenuItem', 'saf', 0));
var_dump ($loader->find ('File', 'saf', 1));

var_dump (class_exists ('File'));

// 5. translatePath()

var_dump ($loader->translatePath ('saf.MailForm.Widget'));

// 6. translateRealPath()

var_dump ($loader->translateRealPath ('foo/bar/asdf/asdf.php'));

?>
--EXPECTF--
object(loader)(6) {
  ["paths"]=>
  array(4) {
    ["default"]=>
    string(%i) "/saf/tests"
    ["saf"]=>
    string(%i) "/saf/lib"
    ["ext"]=>
    string(%i) "/saf/lib/Ext"
    ["pear"]=>
    string(%i) "/saf/lib/PEAR"
  }
  ["included"]=>
  array(3) {
    [0]=>
    string(13) "saf.Functions"
    [1]=>
    string(7) "saf.CGI"
    [2]=>
    string(19) "saf.Template.Simple"
  }
  ["boxPath"]=>
  string(5) "boxes"
  ["formPath"]=>
  string(5) "forms"
  ["app"]=>
  NULL
  ["prefix"]=>
  string(7) "inc/app"
}
bool(true)
bool(true)
bool(false)
int(0)
NULL
string(13) "inc/app/myapp"
string(%i) "/saf/tests"
string(%i) "/saf/lib/GUI/MenuItem.php"
string(%i) "/saf/lib/File/File.php"
bool(true)
string(%i) "/saf/lib/MailForm/Widget/Widget.php"
string(12) "foo.bar.asdf"
