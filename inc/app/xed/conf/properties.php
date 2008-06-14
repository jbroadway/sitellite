<?php

// NOTICE:
//
// You can edit all configurable settings through Sitellite now via
// Control Panel > Admin > Applications
//
// Also note that the spell checker has been disabled, since better
// spell checking solutions with less server-side prerequisites
// exist for all modern browsers -- built-in for all except IE, for
// which we recommend the free IEspell add-on:
//
// http://www.iespell.com/
//

// The path to the aspell/pspell command.
//appconf_set ('pspell_location', 'aspell');
appconf_set ('pspell_location', false);

// Whether to cache the dictionary to the database.  This is useful
// when the pspell PHP extension is not available and the spell checker
// must resort to using a command-line pspell utility.
appconf_set ('pspell_save_suggestions', false);

// The list of supported languages, including their names and encodings.
// To see a list of installed dictionaries, run `aspell dump dicts`
// from the command line.
appconf_set ('pspell_languages', array (
	'en' => array (
		'code' => 'en',
		'name' => 'English',
		'charset' => 'UTF-8',
	),
	/*
	'fr' => array (
		'code' => 'fr',
		'name' => 'Fran&ccedil;ais',
		'charset' => 'ISO-8859-1',
	),
	'es' => array (
		'code' => 'es',
		'name' => 'Espa&ntilde;ol',
		'charset' => 'UTF-8',
	),
	'it' => array (
		'code' => 'it',
		'name' => 'Italiano',
		'charset' => 'UTF-8',
	),
	'ru' => array (
		'code' => 'ru',
		'name' => 'Russian',
		'charset' => 'windows-1251',
	),
	*/
));

// The default language code.
appconf_set ('pspell_default_language', 'en');

// The path to the wvHtml command.  Set to false if unavailable.
// Be sure to include the command itself, ie.
// /usr/local/bin/wvHtml
// You can get the wvWare package from the following website:
// http://www.sourceforge.net/projects/wvware
appconf_set ('wvhtml_location', false);

?>