<?php

if (upgrade_ran ('4.3.2')) {
	page_title (intl_get ('Upgrade Already Applied') . ' (4.3.2)');
	echo '<p><a href="' . site_prefix () . '/index/upgrade-app">') . intl_get ('Back') . '</a></p>';
	return;
}

page_title (intl_get ('Applying Upgrade') . ' (4.3.2)');

echo '<p><strong>' . intl_get ('Applying database updates...') . '</strong></p>';

$version = db_shift ('select version()');

if (version_compare ($version, '5.0.0', 'ge')) {
	db_execute ('alter table sitellite_filesystem change column name name varchar(255) not null default ""');
	db_execute ('alter table sitellite_filesystem change column path path varchar(233) not null default ""');
	db_execute ('alter table sitellite_filesystem_sv change column name name varchar(500) not null default ""');
	db_execute ('alter table sitellite_filesystem_download change column name name varchar(500) not null default ""');
}

// database updates
if (upgrade_db (true)) {
	echo '<p>' . intl_get ('Done.') . '</p>';
} else {
	echo '<p>' . intl_get ('Error') . ': ' . db_error () . '</p>';
	return;
}

echo '<p><a href="' . site_prefix () . '/index/upgrade-app">' . intl_get ('Back') . '</a></p>';

upgrade_log ();

?>