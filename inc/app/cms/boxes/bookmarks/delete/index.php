<?php

loader_import ('cms.Workspace.Bookmark');

$bk = new WorkspaceBookmark;

$bk->delete ($parameters['bk_id']);

header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
exit;

?>