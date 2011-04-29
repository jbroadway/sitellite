<?php

if (! session_admin () || ! isset ($parameters['id'])) {
	header ('Location: ' . site_prefix () . '/index/sitepoll-app');
	exit;
}

loader_import ('sitepoll.Comment');

$c = new SitepollComment;

$comment = $c->get ($parameters['id']);
if (! $comment) {
	header ('Location: ' . site_prefix () . '/index/sitepoll-app');
	exit;
}

$c->remove ($parameters['id']);

page_title (intl_get ('Comment Deleted'));
echo template_simple ('comment_deleted.spt', $comment);

?>