<?php

global $cgi;

loader_import ('siteforum.Post');
loader_import ('siteforum.Filters');

$p = new SiteForum_Post;

global $cgi;

if (! isset ($cgi->limit)) {
	$cgi->limit = 10;
}

if (isset ($parameters['topic'])) {
	$list = $p->getLatest ($cgi->limit, $parameters['topic']);
	$topic = db_shift ('select name from siteforum_topic where id = ?', $parameters['topic']);
	$title = appconf ('rss_title') . ': ' . $topic;
} elseif (isset ($parameters['threads'])) {
	$p->limit = $cgi->limit;
	$list = $p->getThreads ($parameters['threads']);
	$topic = db_shift ('select name from siteforum_topic where id = ?', $parameters['threads']);
	$title = appconf ('rss_title') . ': ' . $topic;
} elseif (isset ($parameters['thread'])) {
	$p->limit = $cgi->limit;
	$list = $p->getThread ($parameters['thread'], true);
	$title = appconf ('rss_title') . ': ' . db_shift ('select subject from siteforum_post where id = ? and post_id = 0', $parameters['thread']);
} else {
	$list = $p->getLatest ($cgi->limit);
	$title = appconf ('rss_title') . ': ' . intl_get ('Latest Postings');
}

foreach (array_keys ($list) as $k) {
	if (! empty ($topic)) {
		$list[$k]->topic = $topic;
	} else {
		$topic = db_shift ('select name from siteforum_topic where id = ?', $list[$k]->topic_id);
		$list[$k]->topic = $topic;
	}
	if (! isset ($list[$k]->body)) {
		$list[$k]->body = db_shift ('select body from siteforum_post where id = ?', $list[$k]->id);
	}
	$list[$k]->summary = preg_replace ('|<strong>([a-zA-Z0-9_-]+) said:(.*)</blockquote>|is', '', $list[$k]->body);
	if (empty ($list[$k]->summary)) {
		$list[$k]->summary = strip_tags ($list[$k]->summary);
	} else {
		$list[$k]->summary = strip_tags ($list[$k]->summary);
	}
	$list[$k]->summary = preg_replace ('|[\r\n\t ]+|s', ' ', $list[$k]->summary);
	if (strlen ($list[$k]->summary) > 128) {
		$list[$k]->summary = substr ($list[$k]->summary, 0, 125) . '...';
	}
}

header ('Content-Type: text/xml');
echo template_simple (
	'rss.spt',
	array (
		'list' => $list,
		'date' => $parameters['date'],
		'rss_title' => $title,
		'rss_description' => appconf ('rss_description'),
		'rss_date' => date ('Y-m-d\TH:i:s') . siteforum_timezone (date ('Z')),
	)
);

exit;

?>