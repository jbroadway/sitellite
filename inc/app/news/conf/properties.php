<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #192 Test all config files for multilingual dates
//

/**
 * Set this to whatever you want the main page title of your news
 * to be.
 */
appconf_set ('news_name', intl_get ('News Stories'));

/**
 * Turns off section menus, creating a single-section news site.
 */
appconf_set ('sections', false);

/**
 * Sets the default number of stories on the section pages.
 */
appconf_set ('limit', 10);

/**
 * Sets the default number of stories on the front page.
 */
appconf_set ('frontpage_limit', 5);

/**
 * Turns comments on or off.  Off by default.
 */
appconf_set ('comments', true);

// Set this to the email address of the forum administrator if you would
// like forum posting notices to be emailed to you.  You may also include
// multiple email addresses by separating them with commas.
appconf_set ('comments_email', false);

// Use the MailForm Security widget to verify that the user is human and
// not an automated comment spam application.  Uses CAPTCHA-like technique.
appconf_set ('comments_security', true);

// Set this to false if you do not want to allow public submissions to your
// web site news.  If you do, then enter one or more email addresses (comma-
// separated) in this property.
appconf_set ('submissions', 'john.luxford@gmail.com');

/**
 * Set to true for comment links also on the front page and section
 * listings, these being "Comments(#)" and "Add Comment".  Off by
 * default.
 */
appconf_set ('comments_blog_style', true);

/**
 * Set this to 'pager' if you want standard pager (Previous 1 2 3 Next)
 * links to navigate between article pages (separated by horizontal rules).
 * Example:
 *
 *     Page: Previous 1 2 3 Next
 *
 * Set this to 'headers' if you want a list of each page header (the first
 * top-level header of each story page, or the first sentence).
 * Example:
 *
 *     Page 1: Introduction
 *     Page 2: Sitellite features one of the mo...
 *     Page 3: For more info
 *
 * Set this to 'prev-next' if you want only the previous and next page
 * header links to display at a time.
 * Example:
 *
 *     Previous: Introduction
 *     Next: For more info
 *
 */
appconf_set ('page_nav_style', 'pager');

/**
 * Set this to 'both' if you want the nav links to appear at the top and
 * bottom of the article.
 *
 * Set this to 'top' if you want the nav links to appear at the top only.
 *
 * Set this to 'bottom' if you want the nav links to appear at the bottom
 * only.
 */
appconf_set ('page_nav_location', 'both');

/**
 * Set to true for RSS links to appear in the bottom navigation links
 * of the news app.  If it is false, RSS feeds are still available
 * via the /index/news-rss-action box, however they simply won't be
 * linked to automatically for you.
 */
appconf_set ('rss_links', true);

/**
 * Set this to whatever you want your RSS <title> field to contain.
 */
appconf_set ('rss_title', site_domain () . ' ' . intl_get ('news'));

/**
 * Set this to whatever you want your RSS <description> field to
 * contain.
 */
appconf_set ('rss_description', intl_get ('News from') . ' ' . site_domain ());

/**
 * Set this to either left or right so that thumbnails will align themselves.
 */
appconf_set ('align_thumbnails', 'right');

define ('NEWS_TODAY', date ('Y-m-d'));
//START: SEMIAS. #192 Test all config files for multilingual dates
//------------------------------
//appconf_set ('shortdate', 'M/d');
//
//appconf_set ('date', 'F j, Y');
//
//appconf_set ('time', 'g:i A');
//
//appconf_set ('date_time', 'M d, Y g:i A');
//------------------------------
appconf_set ('short_date', '%B %e');

appconf_set ('date', '%B %e, %Y');

appconf_set ('time', '%I:%M %p');

appconf_set ('date_time', '%B %e, %Y %I:%M %p');
//END: SEMIAS

appconf_set ('path', site_docroot () . '/inc/app/news/data');

appconf_set ('webpath', site_prefix () . '/inc/app/news/data');

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

/**
 * This loads the settings.ini.php file now so the defaults there can affect
 * subsequent function calls like page_add_style() below in this file.
 */
appconf_default_settings ();

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

	global $cgi;

	$url = 'http://' . site_domain () . site_prefix () . '/index/news-rss-action/nomenu.1';

	if ($cgi->section) {
		$url .= '?section=' . $cgi->section;
	} elseif ($cgi->author) {
		$url .= '?author=' . $cgi->author;
	}

	page_add_link (
		'alternate',
		'application/rss+xml',
		$url
	);
}

?>