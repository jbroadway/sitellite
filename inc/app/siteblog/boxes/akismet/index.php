<?php

page_title (intl_get ('Comment Spam'));

if (! appconf ('akismet_key')) {
	echo '<p>You have not yet enabled the comment spam feature.  To do so, open
the file <tt>"inc/app/siteblog/conf/properties.php"</tt> and enter your Akismet
API key into the <tt>"akismet_key"</tt> field.</p><p>To obtain an Akismet key,
go to <a href="http://wordpress.com/api-keys/">http://wordpress.com/api-keys/</a>
and sign up for a basic account.  On your profile page you will see your API key.
Paste that into the above file to enable Akismet spam filtering.</p>';
	return;
}

// delete anything older than 30 days
db_execute (
	'delete from siteblog_akismet where ts < ?',
	date ('Y-m-d H:i:s', time () - 2592000)
);

$list = db_fetch_array (
	'select * from siteblog_akismet order by ts desc'
);

loader_import ('siteblog.Filters');

echo template_simple ('akismet.spt', array ('list' => $list, 'total' => count ($list)));

?>