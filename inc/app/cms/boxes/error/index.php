<?php

loader_import ('cms.Workflow');

Workflow::trigger (
	'error',
	$parameters
);

page_title (intl_get ('An error occurred'));

echo template_simple ('error.spt', $parameters);

?>