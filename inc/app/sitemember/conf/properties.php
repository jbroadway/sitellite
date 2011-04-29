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
// #179 Digger integrated into sitemember 
//


// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

// This is the email address to send email from in this app.
// The default is webmaster@yourdomain.com
appconf_set ('email', 'webmaster@' . str_replace ('www.', '', site_domain ()));

// This is the default user log-in handler.
appconf_set ('login', 'box:sitemember/login');

// This is the default user log-out handler.
appconf_set ('logout', 'box:sitemember/logout');

// This is the default user registration handler.
// Set this to false to keep registration privately controlled.
appconf_set ('register', 'form:sitemember/register');

// This is the default user home page handler.
appconf_set ('home', 'box:sitemember/home');
//appconf_set ('home', 'box:sitemember/homepage');

// This is a list of member services listed on the member home pages.
// The top-level keys are 'home' and 'profile', which represent the
// two pages on which services can be registered.  The sub-arrays
// consist of a key which is the title of the service, and a value
// which is the handler itself.
//
// If you do not want a title appointed here, you may leave that value
// without a key, and it will know not to display its numeric key.
//
// This separation allows you to specify a service provided only on
// one and not the other, but also to provide separate display views
// for the user on their home page than for other visitors viewing
// that user's home page.
//
// To integrate Digger into your member homepages, activate the following lines
// to your member home and profile services
//
appconf_set ('member_services', array (
	'home' => array (
		intl_get ('Profile') => 'box:sitemember/profile/default',
		//intl_get ('Home Page') => 'box:sitemember/homepage',
		//intl_get ('Banner Ads') => 'box:sitebanner/client',
		//intl_get ('Story Submissions') => 'box:news/my/summary',
		//intl_get ('Event Submissions') => 'box:siteevent/my/summary',
// #179: Semias
        //intl_get ('Digger') => 'box:digger/my/home',
// Semias End
	),
	'profile' => array (
		intl_get ('Profile') => 'box:sitemember/profile/default',
		//intl_get ('Home Page') => 'box:sitemember/homepage',
		//intl_get ('News Stories') => 'box:news/my/stories',
		//intl_get ('Event Listings') => 'box:siteevent/my/events',
// #179: Semias
        //intl_get ('Digger') => 'box:digger/my/profile',
// Semias End
	),
));

// This is the default user password recovery handler.
appconf_set ('passrecover', 'box:sitemember/passrecover');

// This is the default change password handler.
appconf_set ('changepass', 'box:sitemember/changepass');

// This is the default preferences handler.
appconf_set ('preferences', 'form:sitemember/preferences');

// If this is set to 'default', it will use the built-in sidebar content
// for logged-in users.  Otherwise you may specify a box to display as
// an alternate.
appconf_set ('sidebar', 'default');

// If this is set to false, visitor sessions will expire as per usual.
// If this is set to a number, two things will happen: 1) the login
// form will grow a "Remember me" checkbox, and 2) sessions of visitors
// who click said checkbox will only expire after the number of days
// specified here.
appconf_set ('remember_login', 90);

// This is the user profile handler, which displays a user's details
// to the public site.  Set this to false to keep user profiles
// private.
appconf_set ('profile', 'box:sitemember/profile');
//appconf_set ('profile', 'box:sitemember/homepage');

// This is the user list handler, which displays a list of users.
// Set this to false to keep the user list private.
appconf_set ('list', 'box:sitemember/list');

// This is the user contact handler, which allows members to email each
// other through the site without exposing their email addresses.
appconf_set ('contact', 'form:sitemember/contact');

// Set this to the template you wish to use to display the app, otherwise
// the default is used.
appconf_set ('template', false);

// Set this to the template you wish to use to display the member homepages.
appconf_set ('homepage_template', false);

if ($context == 'action') {
	if (appconf ('template')) {
		page_template (appconf ('template'));
	}
}

?>