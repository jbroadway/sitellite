<?php

// your app begins here

if (! $parameters['limit']) {
	$parameters['limit'] = 5;
}

loader_import ('siteforum.Post');
loader_import ('siteforum.Filters');

$p = new SiteForum_Post;

$list = $p->getLatest ($parameters['limit']);

echo template_simple (
	'sidebar.spt',
	array (
		'list' => $list,
	)
);

?>