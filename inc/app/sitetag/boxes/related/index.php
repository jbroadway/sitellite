<?php

if (! isset ($parameters['set'])) {
        $parameters['set'] = 'keywords';
}

if (! isset ($parameters['url'])) {
	$parameters['url'] = $GLOBALS['page']->id;
}
if (! isset ($parameters['limit'])) {
	$parameters['limit'] = 5;
}

loader_import ('sitetag.TagCloud');

$tc = new TagCloud ($parameters['set']);
$items = $tc->getRelated ($parameters['url'], $parameters['limit']);

template_simple_register ('items', $items);
echo template_simple ('related.spt', $parameters);

?>
