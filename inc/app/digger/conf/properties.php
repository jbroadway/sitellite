<?php

// Title to display for the digger app
appconf_set('digger_title', intl_get('Digger'));

// If scores go below this, stories are disabled
appconf_set('ban_threshold', -5);

// Show this many stories per screen
appconf_set('limit', 10);

// This adds the RSS link to each page
if ($context == 'action') {
    page_add_link('alternate',
    'application/rss+xml',
    site_url() . '/index/digger-rss-action'
    );
}

?>