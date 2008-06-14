--TEST--
saf.Site
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Site');

// constructor method

$site = new Site ('$params');

// prefix() method

var_dump ($site->prefix ('$tpl'));

// site_prefix() function

var_dump (site_prefix ());

// site_url() function

var_dump (site_url ());

// site_domain() function

var_dump (site_domain ());

// site_docroot() function

var_dump (site_docroot ());

// site_webpath() function

var_dump (site_webpath ());

// site_secure() function

var_dump (site_secure ());

// site_current() function

var_dump (site_current ());

// site_name() function

var_dump (site_name ());

?>
--EXPECT--
