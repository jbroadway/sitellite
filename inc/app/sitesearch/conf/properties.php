<?php

// Results per screen
appconf_set ('limit', 10);

// Set to true for RSS links to appear in the bottom navigation links
// of the SiteLinks app.  If it is false, RSS feeds are still available
// via the /index/sitelinks-rss-action box, however they simply won't be
// linked to automatically for you.
appconf_set ('rss_links', true);

// Set this to whatever you want your RSS <title> field to contain.
appconf_set ('rss_title', site_domain () . ' ' . intl_get ('Search'));

// Set this to whatever you want your RSS <description> field to
// contain.
appconf_set ('rss_description', intl_get ('Search results from') . ' ' . site_domain ());

appconf_set ('date_format', 'F d, Y \a\t g:ia');

appconf_set ('date_short', 'F d, Y');

appconf_set ('date_month', 'F, Y');

appconf_set ('date_year', 'Y');

appconf_set ('time_format', 'g:ia');

?>