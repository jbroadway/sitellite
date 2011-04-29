--TEST--
saf.I18n.Builder
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.I18n.Builder');

// constructor method

$i18nbuilder = new I18nBuilder ('$root', '$except');

// build() method

var_dump ($i18nbuilder->build ('$path'));

// getList() method

var_dump ($i18nbuilder->getList ());

// getContents() method

var_dump ($i18nbuilder->getContents ('$file'));

// _settings() method

var_dump ($i18nbuilder->_settings ('$file', '$data'));

// _db() method

var_dump ($i18nbuilder->_db ('$file', '$data'));

// _spt() method

var_dump ($i18nbuilder->_spt ('$file', '$data'));

// _tpl() method

var_dump ($i18nbuilder->_tpl ('$file', '$data'));

// _php() method

var_dump ($i18nbuilder->_php ('$file', '$data'));

?>
--EXPECT--
