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

$votes = db_pairs (
	'select choice, count(*) as votes from sitepoll_vote where poll = ? group by choice asc',
	$poll->id
);

$poll->options = array (
	1 => array (),
	2 => array (),
	3 => array (),
	4 => array (),
	5 => array (),
	6 => array (),
	7 => array (),
	8 => array (),
	9 => array (),
	10 => array (),
	11 => array (),
	12 => array (),
);
$total = 0;
foreach ($poll->options as $k => $v) {
	if (empty ($poll->{'option_' . $k})) {
		unset ($poll->options[$k]);
		continue;
	} elseif (! empty ($votes[$k])) {
		$poll->options[$k] = array (
			'answer' => $poll->{'option_' . $k},
			'choice' => $k,
			'votes' => $votes[$k],
			'percent' => 0,
		);
		$total += $votes[$k];
	} else {
		$poll->options[$k] = array (
			'answer' => $poll->{'option_' . $k},
			'choice' => $k,
			'votes' => 0,
			'percent' => 0,
		);
	}
}
foreach ($poll->options as $k => $v) {
	if ($total == 0) {
		$poll->options[$k]['percent'] = '0.00';
	} else {
		$poll->options[$k]['percent'] = number_format (($v['votes'] / $total) * 100, 2);
	}
	$poll->options[$k]['width'] = round ($poll->options[$k]['percent']);
}

$poll->total = $total;

if ($poll->enable_comments == 'yes') {
	$poll->comments = db_fetch_array (
		'select * from sitepoll_comment where poll = ? order by ts asc',
		$poll->id
	);
	$poll->total_comments = count ($poll->comments);
}

loader_import ('sitepoll.Filters');

if ($box['context'] == 'action') {
	page_title (intl_get ('Results') . ': ' . $poll->title);
	$poll->action = true;
} else {
	$poll->action = false;
}
echo template_simple ('results.spt', $poll);

?>