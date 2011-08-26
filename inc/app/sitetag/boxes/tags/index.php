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

if ($tc->canTag) {

	if (! isset ($parameters['name']) || $parameters['name'] == 'sitetag/tags') {
		$parameters['name'] = $GLOBALS['page']->title;
	}
	if (! isset ($parameters['description'])) {
		$parameters['description'] = $GLOBALS['page']->description;
	}

	$taglist = implode (' ', $tc->getAllTags());
	page_add_style (site_prefix () . '/js/jquery.autocomplete.css');
	//page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');
	page_add_script (site_prefix () . '/js/jquery.bgiframe.min.js');
	page_add_script (site_prefix () . '/js/jquery.dimensions.js');
	page_add_script (site_prefix () . '/js/jquery.autocomplete.pack.js');
	page_add_script ('$(document).ready(function(){
                        var data="' . $taglist . '".split(" ");
                        $("#taginput").autocomplete(data,{
                                width: 320,
                                highlight: false,
                                scroll: true,
                                scrollHeight: 300,
                                multiple: true,
                                multipleSeparator: " ",
                                });});');

	page_add_script (site_prefix () . '/js/rpc-compressed.js');
	page_add_script (site_prefix () . '/js/jquery.tinysort.packed.js');
	$parameters['canEdit'] = $tc->canEdit;
	page_add_script (template_simple ('tags.js', $parameters));
}

template_simple_register ('tags', $tags);
echo template_simple ('tags.spt', $tc);

?>
