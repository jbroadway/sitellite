<?php

global $cgi;

if (empty ($cgi->table)) {
	header ('Location: ' . site_prefix () . '/index/myadm-app');
	exit;
}

if (is_array ($cgi->table)) {
	foreach ($cgi->table as $table) {
		if (! db_execute ('delete from ' . $table)) {
			die (db_error ());
		}
	}
	page_title ( 'Database Manager - Cleared tables:');
	echo '<ul>';
	foreach ($cgi->table as $table) {
		echo '<li>' . $table . '</li>';
	}
	echo '</ul>';
	echo template_simple ('<p><a href="{site/prefix}/index/myadm-app">Back</a></p>');
} else {
	if (! db_execute ('delete from ' . $cgi->table)) {
		die (db_error ());
	}
	page_title ( 'Database Manager - Cleared table "' . $cgi->table . '"' );
	echo template_simple ('<p><a href="{site/prefix}/index/myadm-app">Back</a></p>');
}

?>