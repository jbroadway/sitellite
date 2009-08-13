<?php

if (! isset ($parameters['set'])) {
        $parameters['set'] = 'keywords';
}
if (! isset ($parameters['url'])) {
        $parameters['url'] = $GLOBALS['page']->id;
}

loader_import ('sitetag.TagCloud');

$tc = new TagCloud ($parameters['set']);
$tags = $tc->getTags ($parameters['url']);

if (! isset($parameters['showEdit'])) {
        $parameters['showEdit'] = $tc->showEdit;
}
if ($parameters['showEdit'] == 'off'
	|| $parameters['showEdit'] == 'no'
	|| $tc->canTag == false) {
	$parameters['showEdit'] = false;
}

template_simple_register ('tags', $tags);
echo template_simple ('list-tags.spt', $parameters);

?>
