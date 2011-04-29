<?php

loader_import ('wffolderbrowser.PageBrowser');

page_title (intl_get ('Folder Selector'));

$pb = new PageBrowser;

if ($parameters['limit'] == 'yes') {
	$limit = true;
} else {
	$limit = false;
}

$parameters['sections'] = $pb->getSections ($limit);
$parameters['title'] = $pb->getTitle ($parameters['id']);
$parameters['trail'] = $pb->getTrail ($parameters['id'], $limit);
//$parameters['children'] = $pb->getChildren ($parameters['id'], $limit);
$parameters['childfolders'] = $pb->getChildFolders ($parameters['id'], $limit);

loader_import ('saf.GUI.Prompt');

echo template_simple ('browser.spt', $parameters);

exit;

?>