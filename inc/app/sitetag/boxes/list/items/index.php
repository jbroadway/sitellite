<?php

if (! isset ($parameters['set'])) {
        $parameters['set'] = 'keywords';
}

if (! isset ($parameters['tag'])) {
	echo loader_box ('sitetag/cloud', $parameters);
	return;
}

page_title (intl_get ('Pages tagged ') . $parameters['tag']);

loader_import ('sitetag.TagCloud');

$tc = new TagCloud ($parameters['set']);
$items = $tc->getItems ($parameters['tag']);

if ($tc->template) {
	page_template ($tc->template);
}

template_simple_register ('items', $items);
echo template_simple ('list-items.spt', $parameters);

?>
