<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #177 Pagination.
//

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

// Start: SEMIAS #177 Pagination.
//	Not sure a fix is needed here.
$pg = new Pager ($cgi->offset, appconf ('limit'), $p->total);
$pg->setUrl (site_prefix () . '/index/siteforum-topic-action?topic=%s', $cgi->topic);
$pg->getInfo ();
// END: SEMIAS

$t = new SiteForum_Topic;
$topic = $t->getTitle ($cgi->topic);

foreach (array_keys ($list) as $key) {
	$list[$key]->attachments = $p->hasAttachments ($list[$key]->id);
}

page_title ($topic);
template_simple_register ('pager', $pg);
echo template_simple (
	'thread_list.spt',
	array (
		'forum_name' => appconf ('forum_name'),
		'topic' => $topic,
		'list' => $list,
	)
);

if (appconf ('template')) {
	page_template (appconf ('template'));
}

?>
