<?php

$name = db_shift (
	'select name from sitemailer2_newsletter where id = ? and public = "yes"',
	$parameters['list']
);
if (! $name) {
	die ('Unknown list');
}

$items = db_fetch_array (
	'select id, title, date from sitemailer2_message where newsletter = ? and status != "draft" order by date desc',
	$parameters['list']
);

loader_import ('saf.Date');

foreach (array_keys ($items) as $k) {
	$items[$k]->date = Date::format ($items[$k]->date, 'M j, Y');
}

page_title ($name . ' - ' . intl_get ('Archive'));

echo template_simple (
	'public_archive.spt',
	array (
		'name' => $name,
		'items' => $items,
	)
);

?>