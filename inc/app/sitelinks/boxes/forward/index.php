<?php

// your app begins here

loader_import ('sitelinks.Item');

$item = new SiteLinks_Item;

if ($parameters['forward']) { // display item page

	$i = $item->get ($parameters['forward']);
	if ($i) {
		$item->addHit ($parameters['forward']);
		header ('Location: ' . $i->url);
		exit;
	}

}

header ('Location: ' . site_prefix () . '/index/sitelinks-app');
exit;

?>