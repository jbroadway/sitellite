<?php

global $cache;

if (! $cache) {
	page_title (intl_get ('Cache Not Enabled'));
	echo '<p><a href="' . site_prefix () . '/index/cms-cpanel-action">' . intl_get ('Continue') . '</a></p>';
	return;
}

$cache->clear ();

page_title (intl_get ('Cache Cleared'));

echo '<p><a href="' . site_prefix () . '/index/cms-cpanel-action">' . intl_get ('Continue') . '</a></p>';

?>