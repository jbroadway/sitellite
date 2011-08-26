<?php

// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

appconf_set ('title', intl_get ('FAQ'));

// Whether or not to enable user question submissions.
appconf_set ('user_submissions', true);

// Whether or not to collect user details.
appconf_set ('user_details', false);

// Whether or not their email address is required.
appconf_set ('user_email_not_required', false);

// If anonymity is turned on, then administrators do not see user data,
// except in aggregate form.  In fact, the user's name and email address
// are deleted when the user has received a response.  This is ideal for
// such uses as community help services (ie. addictions, teen help, etc.).
appconf_set ('user_anonymity', false);

appconf_set ('user_age_list', array (
	'blank' => '- SELECT -',
	'Under 18' => 'Under 18',
	'18-24' => '18-24',
	'25-29' => '25-29',
	'30-39' => '30-39',
	'40-49' => '40-49',
	'50-59' => '50-59',
	'60-70' => '60-70',
	'Over 70' => 'Over 70',
));

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

appconf_set ('format_date_time', 'F jS, Y - g:i A');

?>