--TEST--
saf.CGI
--FILE--
<?php

$_GET['test1'] = 'testing...';
$_POST['test1'] = 'post...';

include_once ('../init.php');

// 1. basic parameter parsing (constructor and object state)

var_dump ($cgi);

// 2. parseUri()

foreach ($cgi->param as $k => $p) {
	unset ($cgi->{$p});
	unset ($cgi->param[$k]);
}

$_SERVER['REQUEST_URI'] = '/one/two.two/three';

var_dump ($cgi->parseUri ());

// 3. translateUri()

var_dump ($cgi->translateUri ('/foo.php?asdf=fdsa&bar=qwerty'));

var_dump ($cgi->translateUri ('/foo.php?asdf=fdsa&bar=qwerty', 'foo'));

var_dump ($cgi->translateUri ('/foo.php?asdf=fdsa&bar=qwerty', 'foo, php'));

// 4. verify()

function verify1 ($val) {
	return true;
}

$cgi->foo = 'bar';
$cgi->param[] = 'foo';

var_dump ($cgi->verify ('foo', 'regex', '/[a-z]/'));

var_dump ($cgi->verify ('foo', 'func', 'verify1'));

var_dump ($cgi->verify ('foo', 'function', 'verify1'));

var_dump ($cgi->verify ('foo', 'unknown', 'asdf'));

// 5. makeQuery()

$cgi->bar = 'foo';
$cgi->param[] = 'bar';

var_dump ($cgi->makeQuery ());
var_dump ($cgi->makeQuery ('foo'));
var_dump ($cgi->makeQuery (array ('foo', 'bar')));

// 6. verifyRequestMethod()

var_dump ($cgi->verifyRequestMethod ('post'));

$_SERVER['REQUEST_METHOD'] = 'POST';

var_dump ($cgi->verifyRequestMethod ('post'));

var_dump ($cgi->verifyRequestMethod ('get'));

// 7. isHttps()

var_dump ($cgi->isHttps ());

$_SERVER['HTTPS'] = 'on';

var_dump ($cgi->isHttps ());

?>
--EXPECT--
object(CGI)#2 (4) {
  ["param"]=>
  array(2) {
    [0]=>
    string(5) "test1"
    [1]=>
    string(5) "test1"
  }
  ["files"]=>
  array(0) {
  }
  ["error"]=>
  NULL
  ["test1"]=>
  string(7) "post..."
}
array(0) {
}
string(29) "/foo.php/asdf.fdsa/bar.qwerty"
string(21) "/asdf.fdsa/bar.qwerty"
string(21) "/asdf.fdsa/bar.qwerty"
bool(true)
bool(true)
bool(true)
bool(false)
string(16) "?foo=bar&bar=foo"
string(8) "?bar=foo"
string(0) ""
bool(false)
bool(true)
bool(false)
bool(false)
bool(true)
