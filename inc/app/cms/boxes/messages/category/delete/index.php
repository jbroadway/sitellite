<?php

loader_import ('cms.Workspace.Message');

$msg = new WorkspaceMessage;

if (! $msg->deleteCategory ($parameters['category'])) {
	die ($msg->error);
}

header ('Location: ' . site_prefix () . '/index/cms-messages-action');
exit;

?>