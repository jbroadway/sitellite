<?php

if (! appconf ('user_submissions')) {
	header ('Location: ' . site_prefix () . '/index/sitelinks-app');
	exit;
}

if (! session_valid ()) {
	global $cgi;
	page_title (intl_get ('You must be logged in to continue'));
	echo template_simple ('not_registered.spt', $cgi);
	return;
}

loader_import ('cms.Versioning.Rex');
loader_import ('saf.Database.PropertySet');
loader_import ('sitelinks.Filters');

$rex = new Rex ('sitelinks_item');

$res = $rex->getStoreList (
	array (
		'user_id' => new rEqual ('user_id', session_username ()),
	)
);

if (empty ($res)) {
	echo template_simple ('mylinks_none.spt', array ('context' => $context));
	return;
}

$ps = new PropertySet ('sitelinks_item', false);

foreach (array_keys ($res) as $key) {
	$res[$key] = $rex->getCurrent ($res[$key]->id);
	$ps->entity = $res[$key]->id;
	foreach ($ps->get () as $k => $v) {
		$res[$key]->{$k} = $v;
	}
}

if (appconf ('category_screen') == 'default' || ! appconf ('item_pages')) {
	echo template_simple ('mylinks_short.spt', array ('list' => $res, 'context' => $context));
} else {
	echo template_simple ('mylinks_top.spt', array ('context' => $context));
	foreach (array_keys ($res) as $k) {
		if (@file_exists ('inc/app/sitelinks/html/mylinks/' . $res[$k]->ctype . '.spt')) {
			echo template_simple ('mylinks/' . $res[$k]->ctype . '.spt', $res[$k]);
		} else {
			echo template_simple ('mylinks/default.spt', $res[$k]);
		}
	}
}

?>