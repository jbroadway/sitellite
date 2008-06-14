<?php

global $page, $cgi;

if (! session_admin ()) {
	return;
}

global $type;
if ($type != 'document') {
	return;
}

echo CMS_JS_DELETE_CONFIRM;
echo '<a href="/index/cms-delete-action?id=' . $page->id . '"
	onclick="return cms_delete_confirm ()">Delete This Page</a>';

?>