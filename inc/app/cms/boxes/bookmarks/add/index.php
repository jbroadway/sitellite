<?php

global $cgi;

loader_import ('cms.Workspace.Bookmark');

$bk = new WorkspaceBookmark;

$cgi->bk_link = str_replace ('&_msg=deleted', '', $cgi->bk_link);

$res = $bk->add ($cgi->bk_link, $cgi->bk_name);

loader_import ('saf.Misc.RPC');

echo rpc_response ($res);

exit;

?>