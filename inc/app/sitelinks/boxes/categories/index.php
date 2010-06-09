<?php

loader_import ('sitelinks.Item');

$i = new SiteLinks_Item;

$categories = $i->getCategories ();

$data = array (
	'sitesearch' => @file_exists ('inc/app/sitesearch/data/sitesearch.pid'),
	'categories' => $categories,
);

echo template_simple ('categories.spt', $data);

?>