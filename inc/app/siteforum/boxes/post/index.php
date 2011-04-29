<?php

loader_import ('siteforum.Post');

$p = new SiteForum_Post;

$post = $p->get ($parameters['id']);

if (! empty ($post->post_id)) {
	$id = $post->post_id;
} else {
	$id = $post->id;
}

// bug: this doesn't account for the pager on larger threads
// also missing: highlighting of search results

header ('Location: ' . site_prefix () . '/index/siteforum-list-action?post=' . $id . '&highlight=' . $parameters['highlight'] . '#siteforum-message-' . $parameters['id']);
exit;

?>