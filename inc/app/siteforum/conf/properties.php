<?php

// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

// Set this to whatever you want the main page title of your forum to be.
appconf_set ('forum_name', 'Forum');

// Set this to the email address of the forum administrator if you would
// like forum posting notices to be emailed to you.  You may also include
// multiple email addresses by separating them with commas.
appconf_set ('admin_email', false);

// Set this to true or false depending on whether you want to allow members
// to upload files to your forum. Note that this requires write access to
// the inc/app/siteforum/data folder.
appconf_set ('allow_uploads', false);

// Set this to false if you do not want to use a WYSIWYG editor for editing
// posts in your forum.
appconf_set ('use_wysiwyg_editor', true);

// Set this to the member registration form, if you have one.
appconf_set ('register', 'sitemember/register');

// Set this to the member home box, if you have one.
appconf_set ('member_home', 'sitemember/home');

// Set this to the public member profile box, if you have one.
appconf_set ('public_profile', 'sitemember/profile');

// Set to true for RSS links to appear in the bottom navigation links
// of the SiteLinks app.  If it is false, RSS feeds are still available
// via the /index/sitelinks-rss-action box, however they simply won't be
// linked to automatically for you.
appconf_set ('rss_links', true);

// Set this to whatever you want your RSS <title> field to contain.
appconf_set ('rss_title', site_domain () . ' ' . intl_get ('Forum'));

// Set this to whatever you want your RSS <description> field to
// contain.
appconf_set ('rss_description', intl_get ('Postings from') . ' ' . site_domain ());

// Set this to the template you wish to use to display the app, otherwise
// the default is used.
appconf_set ('template', false);

// Set this to the page ID of the page you would like to be the parent of
// your forum.  This affects the web site navigation while within the
// forum itself, and the breadcrumb trail as well.
appconf_set ('page_below', false);

// Set this to the ID of the page which is an alias of the forum.
appconf_set ('page_alias', false);

// Set this to the number of posts to display per screen.
appconf_set ('limit', 10);

if ($context == 'action') {
	page_add_link (
		'alternate',
		'application/rss+xml',
		site_url () . '/index/siteforum-rss-action'
	);

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