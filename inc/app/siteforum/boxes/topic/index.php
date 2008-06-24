<?php

loader_import ('siteforum.Post');
loader_import ('siteforum.Topic');
loader_import ('siteforum.Filters');
loader_import ('saf.GUI.Pager');

global $cgi;

if (empty ($cgi->topic)) {
	header ('Location: ' . site_prefix () . '/index/siteforum-app');
	exit;
}

if (! isset ($cgi->offset) || ! is_numeric ($cgi->offset)) {
	$cgi->offset = 0;
}

$p = new SiteForum_Post;
$p->limit (appconf ('limit'));
$p->offset ($cgi->offset);
$list = $p->getThreads ($cgi->topic);

$pg = new Pager ($cgi->offset, appconf ('limit'), $p->total);
$pg->setUrl (site_prefix () . '/index/siteforum-topic-action?topic=%s', $cgi->topic);
$pg->getInfo ();

$t = new SiteForum_Topic;
$topic = $t->getTitle ($cgi->topic);

page_title ($topic);
template_simple_register ('pager', $pg);
echo template_simple (
	'thread_list.spt',
	array (
		'forum_name' => appconf ('forum_name'),
		'topic' => $topic,
		'list' => $list,
		'sitesearch' => @file_exists ('inc/app/sitesearch/data/sitesearch.pid'),
	)
);

if (appconf ('template')) {
	page_template (appconf ('template'));
}

?>
