<?php

if (upgrade_ran ('4.2.12')) {
	page_title ('Upgrade 4.2.12 Already Applied');
	echo '<p><a href="' . site_prefix () . '/index/upgrade-app">Back</a></p>';
	return;
}

page_title ('Applying Upgrade 4.2.12');

echo '<p><strong>Applying database updates...</strong></p>';

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