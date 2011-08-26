--TEST--
saf.I18n
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.I18n');

// constructor method

$i18n = new I18n ('$directory', '$negotiationMethod', '$load_new');

// setLocale() method

var_dump ($i18n->setLocale ());

// getIndex() method

var_dump ($i18n->getIndex ());

// get() method

var_dump ($i18n->get ('$original', '$obj', '$isHtml'));

// getf() method

var_dump ($i18n->getf ());

// serialize() method

var_dump ($i18n->serialize ('$string'));

// build_keylist() method

var_dump ($i18n->build_keylist ('$basedir', '$except'));

// getLanguage() method

var_dump ($i18n->getLanguage ('$id', '$langfile'));

// getLanguages() method

var_dump ($i18n->getLanguages ('$langfile'));

// negotiate() method

var_dump ($i18n->negotiate ('$method'));

// writeIndex() method

var_dump ($i18n->writeIndex ('$file', '$data'));

// writeLanguage() method

var_dump ($i18n->writeLanguage ('$file', '$data', '$code', '$locale'));

// i18n_get() function

var_dump (i18n_get ('$original', '$obj'));

// i18n_getf() function

var_dump (i18n_getf ());

// i18n_serialize() function

var_dump (i18n_serialize ('$string'));

// intl_get() function

var_dump (intl_get ('$original', '$obj', '$isHtml'));

// intl_getf() function

var_dump (intl_getf ());

// intl_serialize() function

var_dump (intl_serialize ('$string'));

// intl_lang() function

var_dump (intl_lang ());

// intl_locale() function

var_dump (intl_locale ());

// intl_charset() function

var_dump (intl_charset ());

// intl_get_langs() function

var_dump (intl_get_langs ());

// intl_default_lang() function

var_dump (intl_default_lang ());

?>
--EXPECT--
