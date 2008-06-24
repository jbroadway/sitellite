<?php

global $cgi;

if ($cgi->do == 'Done') {
	foreach ($cgi->done as $id) {
		db_execute ('update todo_list set done = now() where id = ?', $id);
	}
	$list = db_fetch_array ('select * from todo_list where id = ?', $id);
} elseif ($cgi->do = 'All') {
	$where = '';
	if (! empty ($cgi->pp)) {
		$where .= ' and person = ' . db_quote ($cgi->pp);
	}
	if (! empty ($cgi->proj)) {
		$where .= ' and project = ' . db_quote ($cgi->proj);
	}
	if (! empty ($cgi->qq)) {
		$where .= ' and todo like ' . db_quote ('%' . $cgi->qq . '%');
	}
	$list = db_fetch_array ('select * from todo_list where done = "0000-00-00 00:00:00" ' . $where);
	db_execute ('update todo_list set done = now() where done = "0000-00-00 00:00:00" ' . $where);
}

if (appconf ('email_notices')) {
	@mail (
		appconf ('email_notices'),
		'To Do - Completed Tasks',
		template_simple ('email_notice.spt', array ('list' => $list)),
		'From: noreply@' . preg_replace ('/^www\./i', '', site_domain ())
	);
}

header ('Location: /index/todo-app?pp=' . $cgi->pp . '&proj=' . $cgi->proj . '&qq=' . $cgi->qq);

exit;

?>
