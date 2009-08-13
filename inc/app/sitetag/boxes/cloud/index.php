<?php

if (! isset ($parameters['set'])) {
        $paramters['set'] = 'keywords';
}

loader_import ('sitetag.TagCloud');

$tc = new TagCloud ($parameters['set']);
$tags = $tc->getTagCloud ();

if (! isset ($parameters['showEdit'])) {
        $parameters['showEdit'] = $tc->showEdit;
}
if ($parameters['showEdit'] == 'no'
	|| $parameters['showEdit'] == 'off'
	|| $tc->canTag == false) {
	$parameters['showEdit'] = false;
}

template_simple_register ('tags', $tags);
echo template_simple ('cloud.spt', $parameters);

?>
