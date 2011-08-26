<?php

// your app begins here

loader_import ('sitelinks.Item');
loader_import ('sitelinks.Filters');

$item = new SiteLinks_Item;

if ($parameters['item']) { // display item page

	$i = $item->get ($parameters['item']);
	if (! $i) {
		header ('Location: ' . site_prefix () . '/index/sitelinks-app');
		exit;
	}

	$item->addView ($parameters['item']);

	$i->sitesearch = @file_exists ('inc/app/sitesearch/data/sitesearch.pid');

	global $cgi;

	if (! empty ($cgi->highlight)) {
		loader_import ('saf.Misc.Search');
		$i->search_bar = search_bar ($cgi->highlight, '/index/sitesearch-app?ctype=sitelinks_item&show_types=yes');
		$queries = search_split_query ($cgi->highlight);
		$i->summary = search_highlight ($i->summary, $queries);
	}

	if (@file_exists ('inc/app/sitelinks/html/full/' . $i->ctype . '.spt')) {
		echo template_simple ('full/' . $i->ctype . '.spt', $i);
	} else {
		echo template_simple ('full/default.spt', $i);
	}

	if (appconf ('user_ratings')) {
		echo loader_box ('sitelinks/ratings', $parameters);
	}

	if (appconf ('show_related')) {
		$related = $item->getRelated ($parameters['item']);
		if (count ($related) > 0) {
			echo template_simple ('related.spt', $related);
		}
	}

} elseif ($parameters['category']) { // display category list

	echo loader_box ('sitelinks/category', $parameters);

} elseif ($parameters['type']) { // display category list

	echo loader_box ('sitelinks/type', $parameters);

} elseif ($parameters['forward']) { // forward click-through

	echo loader_box ('sitelinks/forward', $parameters);

} else { // display main screen

	switch (appconf ('index_screen')) {

		case 'categories':
			echo loader_box ('sitelinks/categories', $parameters);
			break;

		case 'types':
			echo loader_box ('sitelinks/types', $parameters);
			break;

		case 'top':
			echo loader_box ('sitelinks/top', $parameters);
			break;

		case 'newest':
			echo loader_box ('sitelinks/newest', $parameters);
			break;

		default:
			echo loader_box ('sitelinks/category', $parameters);
			break;

	}

}

?>