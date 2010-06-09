<?php

// id, revision_id

loader_import ('cms.Versioning.Parallel');

$p = new Parallel ($parameters['id']);

if (! $p->approve ($parameters['revision_id'])) {
	die ($p->error);
}

header ('Location: ' . site_prefix () . '/index/' . $parameters['id']);
exit;

?>