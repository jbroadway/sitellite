<?php

page_title (intl_get ('Feeds'));

$data = new StdClass ();
$data->screen = 'feeds';

echo template_simple (
	'feeds.spt',
	$data
);

?>