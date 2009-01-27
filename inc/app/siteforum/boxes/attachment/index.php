<?php

$attachment = db_single (
	'select * from siteforum_attachment where post_id = ?',
	$parameters['id']
);

if (! $attachment) {
	echo loader_box ('sitellite/error');
	return;
}

loader_import ('siteforum.Post');

if (! SiteForum_Post::allowed ($parameters['id'])) {
	echo loader_box ('sitellite/error');
	return;
}

header ('Cache-control: private');
header ('Content-type: ' . $attachment->mime);
header ('Content-disposition: attachment; filename=' . $attachment->name);
header ('Content-length: ' . $attachment->size);
readfile ('inc/app/siteforum/data/' . $attachment->post_id);
exit;

?>