<?php

loader_import ('sitelinks.Item');

$i = new SiteLinks_Item;

$types = $i->getTypes ();

$data = array (
	'sitesearch' => @file_exists ('inc/app/sitesearch/data/sitesearch.pid'),
	'types' => $types,
);

echo template_simple ('types.spt', $data);

?>