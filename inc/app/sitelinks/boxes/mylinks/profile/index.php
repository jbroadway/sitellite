<?php

if (! appconf ('user_submissions')) {
	header ('Location: ' . site_prefix () . '/index/sitelinks-app');
	exit;
}

if (! isset ($parameters['user'])) {
	header ('Location: ' . site_prefix () . '/index/sitelinks-app');
	exit;
}

loader_import ('cms.Versioning.Rex');
loader_import ('saf.Database.PropertySet');
loader_import ('sitelinks.Filters');

$rex = new Rex ('sitelinks_item');

$res = $rex->getList (
	array (
		'user_id' => new rEqual ('user_id', $parameters['user']),
	)
);

foreach (array_keys ($res) as $k) {
	$res[$k] = $rex->getCurrent ($res[$k]->id);
}

if ($box['context'] == 'action') {
	page_title (appconf ('sitelinks_name') . ' / ' . intl_get ('By') . ' ' . $parameters['user']);
}

$url = site_prefix () . '/index/sitelinks-app/item.';

echo template_simple (
	'mylinks_profile.spt',
	array (
		'list' => $res,
		'url' => $url,
	)
);

?>