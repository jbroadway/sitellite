<?php

if (! session_admin () || ! isset ($parameters['id'])) {
	header ('Location: ' . site_prefix () . '/index/news-app');
	exit;
}

loader_import ('news.Comment');

$c = new NewsComment;

$comment = $c->get ($parameters['id']);
if (! $comment) {
	header ('Location: ' . site_prefix () . '/index/news-app');
	exit;
}

$c->remove ($parameters['id']);

page_title (intl_get ('Comment Deleted'));
echo template_simple ('comment_deleted.spt', $comment);

?>