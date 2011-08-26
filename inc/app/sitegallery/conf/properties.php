<?php

// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

// If not embedded in another page via the box chooser, this title will be
// used instead.
appconf_set ('title', intl_get ('Galleries'));

// These are the thumbnail dimensions on gallery pages.
appconf_set ('thumbnail_width', 120);
appconf_set ('thumbnail_height', 90);

// This is the folder name under /pix that contains your galleries, so that
// calls to /index/sitegallery-app will show a list of galleries based on
// the subfolders in this location.  Ex: foo for /pix/foo (no initial or
// trailing slash)
appconf_set ('gallery_root', '');

// A list of valid file extensions to show.  Note that not all of these can
// have thumbnails automatically generated.
appconf_set ('valid', array ('jpg', 'gif', 'jpeg', 'bmp', 'png'));

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