<?php

loader_import ('cms.Workspace.Message');

$msg = new WorkspaceMessage;

foreach ($parameters['items'] as $item) {
	$msg->trash ($item);
}

header ('Location: ' . site_prefix () . '/index/cms-messages-category-action?category=' . $parameters['category'] . '&_msg=' . urlencode ('The selected messages have been moved to your trash folder.'));
exit;

?>