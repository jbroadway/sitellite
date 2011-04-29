<?php

// your app begins here

loader_import ('siteforum.Topic');
loader_import ('siteforum.Filters');

$t = new SiteForum_Topic;

$t->orderBy ('name asc');
$list = $t->getTopics ();

/*if (loader_import ('sitetracker.SiteTracker')) {
	$guests = SiteTracker::get (
		array (
			'api_call' => 'num_visitors_online',
			'session_lifetime' => 15,
		)
	);
} else {*/
	$guests = false;
//}

page_title (appconf ('forum_name'));
echo template_simple (
	'topic_list.spt',
	array (
		'list' => $list,
		'users' => db_shift ('select count(*) from sitellite_user'),
		'active' => db_shift ('select count(*) from sitellite_user where session_id is not null and expires >= ?', date ('Y-m-d H:i:s', time() - 3600)),
		'posts' => db_shift ('select count(*) from siteforum_post WHERE ' . session_allowed_sql ()),
		'today' => db_shift ('select count(*) from siteforum_post where ts >= ? AND' . session_allowed_sql (), date('Y-m-d 00:00:00')),
		'week' => db_shift ('select count(*) from siteforum_post where ts >= ? AND ' . session_allowed_sql (), date ('Y-m-d 00:00:00', time () - 604800)),
		'guests' => $guests,
	)
);

?>
