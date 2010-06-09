<?php

// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

/**
 * Set this to the email address(es) that you want to be notified when a
 * banner ad submission is made.
 */
appconf ('email', false);

appconf_set ('date_format_day', 'F jS');

appconf_set ('date_format_week', '\W\e\e\k \o\f F jS');

appconf_set ('date_format_month', 'F, Y');

appconf_set ('date_format_year', 'Y');

appconf_set ('date_format_full', 'F j, Y g:i A');

?>