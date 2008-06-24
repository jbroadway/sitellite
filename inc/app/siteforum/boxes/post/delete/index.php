<?php

loader_import ('siteforum.Post');

$p = new SiteForum_Post;

$post = $p->get ($parameters['id']);

$p->remove ($parameters['id']);

header ('Location: ' . site_prefix () . '/index/siteforum-list-action?topic=' . $post->topic_id . '&post=' . $post->post_id);

exit;

?>