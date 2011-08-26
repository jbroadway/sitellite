<?php

loader_import ('sitelinks.Item');
loader_import ('sitelinks.Filters');

$parameters['category'] = intl_get ('Top-Rated Links');

if (appconf ('category_screen') == 'default' || ! appconf ('item_pages')) {

	$i = new SiteLinks_Item;

	$parameters['list'] = $i->getTop (appconf ('limit'));

	echo template_simple ('top.spt', $parameters);

} else {

	global $cgi;

	$item = new SiteLinks_Item;

	$list = $item->getTop (appconf ('limit'));

	echo template_simple ('category_top.spt', $parameters);

	foreach (array_keys ($list) as $k) {
		$list[$k]->sitelinks_top = true;
		if (@file_exists ('inc/app/sitelinks/html/summary/' . $list[$k]->ctype . '.spt')) {
			echo template_simple ('summary/' . $list[$k]->ctype . '.spt', $list[$k]);
		} else {
			echo template_simple ('summary/default.spt', $list[$k]);
		}
	}

}

?>