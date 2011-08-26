<?php

// This is where app-level variables can be centrally stored.  This file is
// automatically included when the first call to your app is made.  Use the
// appconf_set ('name', 'value'); function to add values here.

$conf = ini_parse ('inc/app/sitewiki/conf/settings.php', false);

foreach ($conf as $k => $v) {
	if ($k == 'template' && $context == 'action') {
		page_template ($v);
	} else {
		appconf_set ($k, $v);
	}
}

appconf_set ('default_page', 'HomePage');

appconf_set ('date_format', 'F j, Y g:i A');

appconf_set ('levels', array (
	'0' => intl_get ('Anyone'),
	'1' => intl_get ('Registered Users Only'),
	'2' => intl_get ('Admin-Level Users Only'),
	'3' => intl_get ('Owner Only'),
));

appconf_set ('yesno', array (
	'0' => intl_get ('No'),
	'1' => intl_get ('Yes'),
));

//page_template ('full');

page_add_link (
	'alternate',
	'application/rss+xml',
	site_url () . '/index/sitewiki-feeds-short-action'
);

page_add_link (
	'alternate',
	'application/rss+xml',
	site_url () . '/index/sitewiki-feeds-full-action'
);

?>