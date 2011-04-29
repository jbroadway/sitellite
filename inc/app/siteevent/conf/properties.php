<?php

// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

appconf_set ('siteevent_title', intl_get ('Events Calendar'));

// Set this to false if you do not want to allow public submissions to your
// web site events.  If you do, then enter one or more email addresses (comma-
// separated) in this property.
appconf_set ('submissions', false);

// This is the default view for the main SiteEvent page.  Default is 'month'
// and other valid options are 'week' and 'day'.
appconf_set ('default_view', 'month');

// Set this to the template you wish to use to display the app, otherwise the
// default is used.
appconf_set ('template', 'wide');

// Set this to the template you wish to use to display the full calendar screen,
// otherwise the value from the app-wide template setting is used.
appconf_set ('template_calendar', 'wide');

// The location of the CSS for the calendar.
appconf_set ('css_location', site_prefix () . '/inc/app/siteevent/html/style.css');

// The default city for new events.
appconf_set ('default_city', false);

// The default province for new events.
appconf_set ('default_province', false);

// The default country for new events.
appconf_set ('default_country', false);

// Set this to the page ID of the page you would like to be the parent of
// the app.  This affects the web site navigation while within the
// app itself, and the breadcrumb trail as well.
appconf_set ('page_below', false);

// Set this to the ID of the page which is an alias of the app.
appconf_set ('page_alias', false);

// Set to true for iCalendar links to appear in the bottom navigation links
// of the SiteEvent app.  If it is false, RSS feeds are still available
// via the /index/siteevents-ical-action box, however they simply won't be
// linked to automatically for you.
appconf_set ('ical_links', true);

// Set to true for RSS links to appear in the bottom navigation links
// of the SiteEvent app.  If it is false, RSS feeds are still available
// via the /index/siteevents-rss-action box, however they simply won't be
// linked to automatically for you.
appconf_set ('rss_links', true);

// Set this to whatever you want your RSS <title> field to contain.
appconf_set ('rss_title', site_domain () . ' ' . intl_get ('Events'));

// Set this to whatever you want your RSS <description> field to
// contain.
appconf_set ('rss_description', intl_get ('Event listings from') . ' ' . site_domain ());

// Set this to false if you want to remove the direction links to Google Maps.
appconf_set ('google_maps', true);

define ('SITEEVENT_TODAY', date ('Y-m-d'));

if (date ('i') < 30) {
	define ('SITEEVENT_NOW', date ('H:i:00', time () - (date ('i') * 60) + 1800));
} else {
	define ('SITEEVENT_NOW', date ('H:00:00', time () + (3600 - (date ('i') * 60))));
}

appconf_set ('date_format', '%B %e, %Y');
appconf_set ('short_date', '%B %e');

// This loads the settings.ini.php file now so the defaults there can affect
// subsequent function calls like page_add_style() below in this file.
appconf_default_settings ();

formdata_set (
	'hours',
	array (
		'00:00:00' => '- SELECT -',
		'08:00:00' => '&nbsp;8:00 AM',
		'08:30:00' => '&nbsp;8:30 AM',
		'09:00:00' => '&nbsp;9:00 AM',
		'09:30:00' => '&nbsp;9:30 AM',
		'10:00:00' => '10:00 AM',
		'10:30:00' => '10:30 AM',
		'11:00:00' => '11:00 AM',
		'11:30:00' => '11:30 AM',
		'12:00:00' => '12:00 PM',
		'12:30:00' => '12:30 PM',
		'13:00:00' => '&nbsp;1:00 PM',
		'13:30:00' => '&nbsp;1:30 PM',
		'14:00:00' => '&nbsp;2:00 PM',
		'14:30:00' => '&nbsp;2:30 PM',
		'15:00:00' => '&nbsp;3:00 PM',
		'15:30:00' => '&nbsp;3:30 PM',
		'16:00:00' => '&nbsp;4:00 PM',
		'16:30:00' => '&nbsp;4:30 PM',
		'17:00:00' => '&nbsp;5:00 PM',
		'17:30:00' => '&nbsp;5:30 PM',
		'18:00:00' => '&nbsp;6:00 PM',
		'18:30:00' => '&nbsp;6:30 PM',
		'19:00:00' => '&nbsp;7:00 PM',
		'19:30:00' => '&nbsp;7:30 PM',
		'20:00:00' => '&nbsp;8:00 PM',
		'20:30:00' => '&nbsp;8:30 PM',
		'21:00:00' => '&nbsp;9:00 PM',
		'21:30:00' => '&nbsp;9:30 PM',
		'22:00:00' => '10:00 PM',
		'22:30:00' => '10:30 PM',
		'23:00:00' => '11:00 PM',
		'23:30:00' => '11:30 PM',
	)
);

formdata_set (
	'recurring',
	array (
		'no' => '- ' . intl_get ('SELECT') . ' -',
		'daily' => intl_get ('Daily'),
		'weekly' => intl_get ('Weekly'),
		'monthly' => intl_get ('Monthly'),
		'yearly' => intl_get ('Yearly'),
	)
);

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

	$url = 'http://' . site_domain () . site_prefix () . '/index/siteevent-rss-action';
	$url2 = 'http://' . site_domain () . site_prefix () . '/index/siteevent-ical-action';

	if ($cgi->category) {
		$url .= '?category=' . $cgi->category;
		$url2 .= '?category=' . $cgi->category;
	} elseif ($cgi->user) {
		$url .= '?user=' . $cgi->user;
		$url2 .= '?user=' . $cgi->user;
	}

	page_add_link (
		'alternate',
		'application/rss+xml',
		$url
	);

	page_add_link (
		'alternate',
		'text/calendar',
		$url2
	);

	page_add_style (
		appconf ('css_location')
	);
}

loader_import ('siteevent.Filters');

?>