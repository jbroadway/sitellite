<?php

if (session_admin ()) {
	$clause = session_allowed_sql ();
} else {
	$clause = session_approved_sql ();
}

$list = db_fetch_array (
	'select
		id, title, year(date_added) as year, date_added
	from
		sitepoll_poll
	where ' . $clause . '
	order by
		date_added desc'
);

loader_import ('sitepoll.Poll');

$p = new SitePoll;
$p->usePermissions = true;
$p->multilingual = true;

$p->orderBy ('date_added desc');
$list = $p->find (array ());
foreach ($list as $k => $v) {
	$list[$k]->year = substr ($v->date_added, 0, 4);
}

$years = array ();
foreach ($list as $k => $v) {
	if (! is_array ($years[$v->year])) {
		$years[$v->year] = array ($v);
	} else {
		$years[$v->year][] = $v;
	}
}

loader_import ('sitepoll.Filters');

if ($box['context'] == 'action') {
	page_title (intl_get ('Poll Archive'));
}

echo template_simple ('past.spt', $years);

?>