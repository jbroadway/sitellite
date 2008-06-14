<?php

loader_import ('cms.Workspace.Bookmark');

$bk = new WorkspaceBookmark;

$bookmarks = $bk->getList ();
if (! is_array ($bookmarks)) {
	echo '<p>' . intl_get ('No bookmarks.') . '</p>';
	return;
}

echo template_simple ('bookmarks.spt', array ('bookmarks' => $bookmarks));

?>