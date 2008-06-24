<?php

loader_import ('siteforum.Post');
loader_import ('siteforum.Topic');
loader_import ('siteforum.Filters');
loader_import ('saf.GUI.Pager');

global $cgi;

if (empty ($cgi->post)) {
	header ('Location: ' . site_prefix () . '/index/siteforum-topic-action?topic=' . $cgi->topic);
	exit;
}

if (! isset ($cgi->offset) || ! is_numeric ($cgi->offset)) {
	$cgi->offset = 0;
}

$p = new SiteForum_Post;
$p->orderBy ('ts asc');
$p->limit (appconf ('limit'));
$p->offset ($cgi->offset);
$list = $p->getThread ($cgi->post);

if (! empty ($cgi->highlight)) {
	$highlight = '?highlight=' . $cgi->highlight;
} else {
	$highlight = '?highlight=';
}

$pg = new Pager ($cgi->offset, appconf ('limit'), $p->total);
$pg->setUrl (site_prefix () . '/index/siteforum-list-action/post.%s' . $highlight, $cgi->post);
$pg->getInfo ();

if (! $cgi->topic) {
	$cgi->topic = $list[0]->topic_id;
}

$t = new SiteForum_Topic;
$topic = $t->getTitle ($cgi->topic);

$subject = $list[0]->subject;

if (! empty ($cgi->highlight)) {
	loader_import ('saf.Misc.Search');
	echo search_bar ($cgi->highlight, '/index/sitesearch-app?ctype=siteforum_post&show_types=yes');
	$queries = search_split_query ($cgi->highlight);
	foreach (array_keys ($list) as $key) {
		$list[$key]->body = search_highlight ($list[$key]->body, $queries);
	}
}

page_title ($subject);
template_simple_register ('pager', $pg);
echo template_simple (
	'message_list.spt',
	array (
		'forum_name' => appconf ('forum_name'),
		'topic' => $topic,
		'subject' => $subject,
		'list' => $list,
		'sitesearch' => @file_exists ('inc/app/sitesearch/data/sitesearch.pid'),
	)
);

if (appconf ('template')) {
	page_template (appconf ('template'));
}

?>
