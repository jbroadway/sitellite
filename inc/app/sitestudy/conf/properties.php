<?php

// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

// Set this to the template you wish to use to display the app, otherwise the
// default is used.
appconf_set ('template', false);

// Set this to the page ID of the page you would like to be the parent of
// the app.  This affects the web site navigation while within the
// app itself, and the breadcrumb trail as well.
appconf_set ('page_below', false);

// Set this to the ID of the page which is an alias of the app.
appconf_set ('page_alias', false);

if ($context == 'action') {
	if (appconf ('page_below')) {
		page_below (appconf ('page_below'));
	}
	if (appconf ('page_alias')) {
		page_id (appconf ('page_alias'));
	}
	if (appconf ('template')) {
		page_template (appconf ('template'));
	}
}

?>