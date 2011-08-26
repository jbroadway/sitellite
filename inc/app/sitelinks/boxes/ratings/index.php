<?php

// check if the user has voted already

loader_import ('sitelinks.Item');

$item = new SiteLinks_Item;

if ($item->hasVoted ($parameters['item'])) {
	$parameters['has_voted'] = true;
} else {
	$parameters['has_voted'] = false;
}

if (! $parameters['has_voted'] && isset ($parameters['rating'])) {
	$item->addRating ($parameters['item'], $parameters['rating']);

	page_title (intl_get ('Thank You'));
	echo template_simple ('ratings_thanks.spt', $parameters);
} else {
	$ratings = $item->rating ($parameters['item']);
	$parameters['votes'] = $ratings->votes;
	$parameters['ratings'] = $ratings->rating;
	echo template_simple ('ratings.spt', $parameters);
}

?>