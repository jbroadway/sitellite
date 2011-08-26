<?php

global $page, $cgi;

if (! session_admin ()) {
	return;
}

global $type;
if ($type != 'document') {
	return;
}

echo '<a href="/index/cms-edit-form?id=' . $page->id . '">Edit This Page</a>';

?>