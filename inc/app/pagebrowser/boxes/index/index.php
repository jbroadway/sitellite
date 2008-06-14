<?php

loader_import ('pagebrowser.PageBrowser');

page_title (intl_get ('Page Selector'));

$pb = new PageBrowser;

if ($parameters['limit'] == 'yes') {
	$limit = true;
} else {
	$limit = false;
}

$parameters['sections'] = $pb->getSections ($limit);
$parameters['title'] = $pb->getTitle ($parameters['id']);
$parameters['trail'] = $pb->getTrail ($parameters['id'], $limit);
$parameters['children'] = $pb->getChildren ($parameters['id'], $limit);

echo template_simple ('browser.spt', $parameters);

exit;

?>