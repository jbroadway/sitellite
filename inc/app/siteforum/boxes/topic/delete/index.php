<?php

loader_import ('siteforum.Topic');

$t = new SiteForum_Topic;

$t->remove ($parameters['id']);

header ('Location: ' . site_prefix () . '/index/siteforum-app');

exit;

?>
