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
// #192 Test all config files for multilingual dates.

// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

/**
 * Set this to the name you want to display as the link section name.
 */
appconf_set ('sitelinks_name', intl_get ('Links'));

/**
 * Set this to the name you want to display as the link section name.
 */
appconf_set ('sitelinks_name_singular', intl_get ('Link'));

/**
 * Set this to the item type that you want the add/edit/submission forms
 * to default to.  If you want a different default for different sections,
 * make this an associative array with the sections as keys and the types
 * as values, ie.
 *
 * appconf_set ('sitelinks_default_type', array (
 *     'default' => 'software',
 *     'Software' => 'software',
 *     'Templates' => 'template',
 *     'Web Sites' => 'company',
 *     'Music' => 'music',
 * ));
 *
 */
appconf_set ('sitelinks_default_type', 'default');

/**
 * Set this to true if you want item links to first go to their own page
 * before going to the link site.  Please note: This is required for
 * user ratings (below), as that is the template they appear in.
 */
appconf_set ('item_pages', true);

/**
 * Set this to true if you want items to be browseable by type as well as
 * by category.
 */
appconf_set ('browse_by_type', true);

/**
 * Set this to true if you want users (visitors) to be able to rate items
 * in your list.
 */
appconf_set ('user_ratings', true);

/**
 * Set this to true if you want users (visitors) to be able to make
 * submissions to your list.
 */
appconf_set ('user_submissions', true);

/**
 * Set this to the email address you want user submission notifications
 * to go to, or false if you don't want to receive such emails.
 */
appconf_set ('email_user_submissions', false);

/**
 * Set this to one of the following to determine what to display on the
 * main screen (ie. /index/sitelinks-app):
 *
 * - 'categories' - A list of categories.
 * - 'types' - A list of types.
 * - 'top' - The most popular items (if user_ratings is true).
 * - 'newest' - The most recently added or updated items.
 * - 'default' - A default list, useful for single-page link lists.
 */
appconf_set ('index_screen', 'categories');

/**
 * Set this to one of the following to determine how to display the
 * category listing screens (ie. /index/sitelinks-app/category.CatName):
 *
 * - 'full' - A full template of each listing including summary.  Please
 *   note that this view requires 'item_pages' to be set to true, and
 *   will display as 'default' if it is not.
 * - 'default' - A default bulleted list.
 */
appconf_set ('category_screen', 'full');

/**
 * If 'category_screen' is set to 'full', then a pager is used when
 * there are too many items to display on a single screen.  This number
 * is determined by the 'limit' setting.
 */
appconf_set ('limit', 10);

/**
 * If this is set to true, it will display a list of links that are
 * attributed to the same user_id.
 */
appconf_set ('show_related', true);

/**
 * Set to true for RSS links to appear in the bottom navigation links
 * of the SiteLinks app.  If it is false, RSS feeds are still available
 * via the /index/sitelinks-rss-action box, however they simply won't be
 * linked to automatically for you.
 */
appconf_set ('rss_links', true);

/**
 * Set this to whatever you want your RSS <title> field to contain.
 */
appconf_set ('rss_title', site_domain () . ' ' . intl_get ('links'));

/**
 * Set this to whatever you want your RSS <description> field to
 * contain.
 */
appconf_set ('rss_description', intl_get ('Links from') . ' ' . site_domain ());

/**
 * These are a few date display format settings.  See www.php.net/date
 * for more info on the formatting syntax.
 */
//START: SEMIAS. #192 Test all config files for multilingual dates.
//-----------------------------------------------
//appconf_set ('shortdate', 'M/d');
//appconf_set ('date', 'F j, Y');
//appconf_set ('time', 'g:i A');
//appconf_set ('date_time', 'M d, Y g:i A');
//appconf_set ('admin_date_short', 'M d, Y');
//appconf_set ('admin_date_month', 'M, Y');
//appconf_set ('admin_date_year', 'Y');
//-----------------------------------------------
appconf_set ('shortdate', '%B %e'); // in all other apps it is short_date instead of shortdate
appconf_set ('short_date', '%B %e');
appconf_set ('date', '%B %e, %Y');
appconf_set ('time', '%I:%M %p');
appconf_set ('date_time', '%B %e, %Y %I:%M %p');
appconf_set ('admin_date_short', '%B %e');
appconf_set ('admin_date_month', '%B, %Y');
appconf_set ('admin_date_year', '%Y');
//END: SEMIAS.

formdata_set ('types_software_cost', array (
	'Public Domain',
	'Open Source/GPL',
	'Open Source/LGPL',
	'Open Source/BSD',
	'Open Source/Apache',
	'Open Source/Other',
	'Free/Closed Source',
	'Shareware',
	'Commercial/$1-50',
	'Commercial/$50-100',
	'Commercial/$100-200',
	'Commercial/$200-300',
	'Commercial/$300-500',
	'Commercial/$500-1,000',
	'Commercial/Over $1,000',
));

formdata_set ('types_software_platform', array (
	'Windows Desktop',
	'Linux Desktop/KDE',
	'Linux Desktop/GNOME',
	'Linux Desktop/Other',
	'Mac OS X Desktop',
	'Windows/Mac OS X Desktop',
	'Windows/Linux Desktop',
	'Linux/Mac OS X Desktop',
	'All Desktops',
	'Windows Server',
	'Linux Server',
	'Mac OS X Server',
	'Windows/Mac OS X Server',
	'Windows/Linux Server',
	'Linux/Mac OS X Server',
	'All Servers',
));

formdata_set ('types_music_genre', array (
	'Alternative',
	'Blues',
	'Broadway',
	'Classic Rock',
	'Classical',
	'Country',
	'Dance',
	'Doo Wop',
	'Electronic',
	'Folk',
	'Hard Rock',
	'Hip Hop/Rap',
	'Inspirational',
	'Jazz',
	'Latin',
	'Metal',
	'Motown',
	'New Age',
	'Opera',
	'Pop',
	'Punk',
	'R&B/Soul',
	'Rock \'n\' Roll',
	'Vocal',
	'World',
));

?>