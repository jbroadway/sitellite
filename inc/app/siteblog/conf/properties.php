<?php

/**
 * Set this to the name of your blog.
 */
appconf_set ('blog_name', intl_get ('My Blog'));

/**
 * Use the MailForm Security widget to verify that the user is human and
 * not an automated comment spam application.  Uses CAPTCHA-like technique.
 */
appconf_set ('comments_security', true);

/**
 * This enables the Akismet.com comment spam filtering service on SiteBlog
 * comments.  Go to http://wordpress.com/api-keys/ and sign up for a basic
 * account (you don't need a full blog, just a user account).  On your profile
 * page you will see your API key.  Paste that here to enable Akismet spam
 * filtering.
 */
appconf_set ('akismet_key', false);

/**
 * Set this to the template you wish to use to display the app, otherwise
 * the default is used.
 */
appconf_set ('template', false);

/**
 * Set this to the page ID of the page you would like to be the parent of
 * the app.  This affects the web site navigation while within the
 * app itself, and the breadcrumb trail as well.
 */
appconf_set ('page_below', false);

/**
 * Set this to the ID of the page which is an alias of the app.
 */
appconf_set ('page_alias', false);

appconf_set ('limit', 10);

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

	$url = site_url () . '/index/siteblog-rss-action/nomenu.1';
	
	page_add_link (
		'alternate',
		'application/rss+xml',
		$url
	);
}

?>