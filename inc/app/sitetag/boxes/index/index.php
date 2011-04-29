<?php

if (! isset ($parameters['set'])) {
        $parameters['set'] = 'keywords';
}

loader_import ('sitetag.TagCloud');
$tc = new TagCloud ($parameters['set']);

if (isset ($parameters['tag'])) {
	page_title (intl_get ('Pages tagged ') . $parameters['tag']);

	$items = $tc->getItems ($parameters['tag']);

	if ($tc->template) {
		page_template ($tc->template);
	}

	template_simple_register ('items', $items);
	echo template_simple ('list-items.spt', $parameters);
}
else {
	page_title (intl_get ('All Tags'));

	$tags = $tc->getTagCloud ();

	template_simple_register ('tags', $tags);
	echo template_simple ('cloud.spt', $parameters);
}

?>
