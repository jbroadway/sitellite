<?php

loader_import ('cms.Workspace.Message');

$msg = new WorkspaceMessage;

if ($parameters['new_folder'] == 'Inbox') {
	$parameters['new_folder'] = '';
}

foreach ($parameters['items'] as $item) {
	$msg->setCategory ($item, $parameters['new_folder']);
}

header ('Location: ' . site_prefix () . '/index/cms-messages-category-action?category=' . $parameters['category']);
exit;

?>