<?php

global $cgi;

if ($cgi->click) {
    //track subscription
    db_execute ('update sitemailer2_newsletter set rss_subs=rss_subs+1 where id = ?', $parameters['list']);
}

$name = db_shift (
	'select name from sitemailer2_newsletter where id = ? and public = "yes"',
	$parameters['list']
);
if (! $name) {
	die ('Unknown list');
}

$items = db_fetch_array (
	'select id, title, date from sitemailer2_message where newsletter = ? and status != "draft" order by date desc limit 10',
	$parameters['list']
);

loader_import ('sitemailer2.Functions');
loader_import ('saf.Date');

$z = sitemailer2_rss_timezone (date ('Z'));

foreach (array_keys ($items) as $k) {
	$items[$k]->date = Date::format ($items[$k]->date, 'Y-m-d\TH:i:s') . $z;
}

header ('Content-Type: text/xml');

echo template_simple (
	'public_rss.spt',
	array (
		'name' => $name,
		'items' => $items,
		'date' => date ('Y-m-d\TH:i:s') . $z,
	)
);

exit;

?>