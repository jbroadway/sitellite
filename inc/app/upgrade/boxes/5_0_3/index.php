<?php

if (upgrade_ran ('5.0.3')) {
	page_title (intl_get ('Upgrade Already Applied') . ' (5.0.3)');
	echo '<p><a href="' . site_prefix () . '/index/upgrade-app">' . intl_get ('Back') . '</a></p>';
	return;
}

page_title (intl_get ('Applying Upgrade') . ' (5.0.3)');

echo '<p><strong>' . intl_get ('Applying database updates...') . '</strong></p>';

// database updates
if (upgrade_db (true)) {
	echo '<p>Done.</p>';
} else {
	echo '<p>Error: ' . db_error () . '</p>';
	return;
}

echo '<p><a href="' . site_prefix () . '/index/upgrade-app">Back</a></p>';

upgrade_log ();

?>