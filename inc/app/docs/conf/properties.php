<?php

// set the initial title here
appconf_set ('title', 'Sitellite API');

// this will allow more than one codebase to be documented, the core saf/lib
// as well as libraries for the various Sitellite apps.
appconf_set ('codebases', array (
	'saf' => 'saf/lib',
	'cms' => 'inc/app/cms/lib',
	'multilingual' => 'inc/app/multilingual/lib',
	'news' => 'inc/app/news/lib',
	'xed' => 'inc/app/xed/lib',
));

// these rules will forward packages that end up in the wrong parts of the tree,
// or which actually belong to 3rd party libraries (that we've inherited from)
// such as PEAR.
appconf_set ('forward_rules', array (
	'saf.Database:I18n' => 'saf.I18n:I18n',
	'saf.XML:Parser' => 'saf.Parser:Parser',
	'saf.HTML:Parser' => 'saf.Parser:Parser',
	'saf.Template:XT' => 'saf.XML:XT',
	'saf.HTML:XML_HTMLSax3' => 'http://pear.php.net/package/XML_HTMLSax3',
	'saf.File:Directory' => 'http://www.php.net/manual/en/class.dir.php',
	'saf.Date:HtmlLayout' => 'saf.GUI:HtmlLayout', // todo: this class is dynamically generated, how to document?
));

?>