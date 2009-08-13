<?php

if (! isset ($parameters['set'])) {
        $paramters['set'] = 'keywords';
}

if (! isset ($parameters['url'])) {
	$parameters['url'] = $GLOBALS['page']->id;
}
if (! isset ($parameters['num'])) {
	$parameters['num'] = 5;
}

loader_import ('sitetag.TagCloud');

$tc = new TagCloud ($parameters['set']);
$items = $tc->getRelated ($parameters['url'], $parameters['num']);

template_simple_register ('items', $items);
echo template_simple ('related.spt', $parameters);

?>
