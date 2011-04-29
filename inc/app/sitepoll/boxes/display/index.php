<?php

/*
if (session_admin ()) {
	$clause = session_allowed_sql ();
} else {
	$clause = session_approved_sql ();
}
*/

loader_import ('sitepoll.Poll');

$p = new SitePoll;
$p->usePermissions = true;
$p->multilingual = true;

if (! empty ($parameters['poll'])) {
	$poll = $p->get ($parameters['poll']);
} else {
	$p->orderBy ('date_added desc');
	$p->limit (1);
	$poll = array_shift ($p->find (array ()));
}

/*
if (! empty ($parameters['poll'])) {
	$poll = db_single ('select * from sitepoll_poll where id = ? and ' . $clause, $parameters['poll']);
} else {
	$poll = db_single ('select * from sitepoll_poll where ' . $clause . ' order by date_added desc limit 1');
}
*/

$poll->voted = db_shift (
	'select count(*) from sitepoll_vote where poll = ? and ua = ? and ip = ?',
	$poll->id,
	$_SERVER['HTTP_USER_AGENT'],
	$_SERVER['REMOTE_ADDR']
);

if ($box['context'] == 'action') {
	page_title (intl_get ('Poll') . ': ' . $poll->title);
	$poll->action = true;
} else {
	$poll->action = false;
}
echo template_simple ('display.spt', $poll);

?>