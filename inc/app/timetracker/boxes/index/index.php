<?php

if (! session_admin ()) {
	page_title ( 'TimeTracker - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="timetracker-app" />
		<table cellpadding="5" border="0">
			<tr>
				<td>Username</td>
				<td><input type="text" name="username" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" name="password" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Enter" /></td>
			</tr>
		</table>
		</form>'
	);

	return;
}

page_title ('TimeTracker');

$data = array ('added' => $parameters['added']);

$res = db_fetch ('select * from timetracker_project order by name asc');
if (! $res) {
	$res = array ();
} elseif (is_object ($res)) {
	$res = array ($res);
}

$data['projects'] = $res;

// figure out some dates...
if (! empty ($parameters['year'])) {
	$y = $parameters['year'];
} else {
	$y = date ('Y');
}
if (! empty ($parameters['month'])) {
	$m = $parameters['month'];
} else {
	$m = date ('m');
}
$t = date ('t', mktime (0, 0, 0, $m, 1, $y));

$d1 = 1;
$d1w = date ('w', mktime (0, 0, 0, $m, $d1, $y));
$d2 = date ('t', mktime (0, 0, 0, $m, 1, $y));
$d2w = date ('w', mktime (0, 0, 0, $m, $d2, $y));
$data['currMonth'] = date ('M', mktime (0, 0, 0, $m, 1, $y));
$data['prevMonthShow'] = date ('M', mktime (0, 0, 0, $m - 1, 1, $y));
$data['prevMonth'] = date ('m', mktime (0, 0, 0, $m - 1, 1, $y));
$data['prevYear'] = date ('Y', mktime (0, 0, 0, $m - 1, 1, $y));
$data['nextMonthShow'] = date ('M', mktime (0, 0, 0, $m + 1, 1, $y));
$data['nextMonth'] = date ('m', mktime (0, 0, 0, $m + 1, 1, $y));
$data['nextYear'] = date ('Y', mktime (0, 0, 0, $m + 1, 1, $y));

$weeks = array (
	array (
		'start' => $d1,
		'end' => (7 - $d1w),
	),
	array (
		'start' => ((7 - $d1w) + 1),
		'end' => ((7 - $d1w) + 7),
	),
	array (
		'start' => ((7 - $d1w) + 8),
		'end' => ((7 - $d1w) + 14),
	),
	array (
		'start' => ((7 - $d1w) + 15),
		'end' => ((7 - $d1w) + 21),
	),
	array (
		'start' => ((7 - $d1w) + 22),
		'end' => ((7 - $d1w) + 28),
	),
	array (
		'start' => ((7 - $d1w) + 29),
		'end' => ((7 - $d1w) + 35),
	),
);
if ($weeks[count ($weeks) - 1]['start'] > $t) {
	array_pop ($weeks);
}
if ($weeks[count ($weeks) - 1]['end'] > $t) {
	$weeks[count ($weeks) - 1]['end'] = $t;
}

$res = db_fetch ('select username from sitellite_user');
if (! $res) {
	$res = array ();
} elseif (is_object ($res)) {
	$res = array ($res);
}

foreach ($res as $key => $row) {
	$res[$key]->total = db_shift (
		'select sum(e.duration)
		from timetracker_entry e, timetracker_user_entry u
		where u.entry_id = e.id and u.user_id = ?',
		$row->username
	);
	$res[$key]->month = db_shift (
		'select sum(e.duration)
		from timetracker_entry e, timetracker_user_entry u
		where u.entry_id = e.id and u.user_id = ? and e.started between ? and ?',
		$row->username,
		$y . '-' . $m . '-01 00:00:00',
		$y . '-' . $m . '-' . $t . ' 23:59:59'
	);
	$res[$key]->weeks = array ();
	foreach ($weeks as $k => $week) {
		$sum = db_shift (
			'select sum(e.duration)
			from timetracker_entry e, timetracker_user_entry u
			where u.entry_id = e.id and u.user_id = ? and e.started between ? and ?',
			$row->username,
			$y . '-' . $m . '-' . $week['start'] . ' 00:00:00',
			$y . '-' . $m . '-' . $week['end'] . ' 23:59:59'
		);
		$res[$key]->weeks[] = $sum;
		$weeks[$k]['total'] += $sum;
	}
	$data['month_total'] += $res[$key]->month;
	$data['grand_total'] += $res[$key]->total;
}

$data['employees'] = $res;
$data['weeks'] = $weeks;

//echo '<pre>';
//print_r ($data);
//echo '</pre>';
//exit;

loader_import ('timetracker.Filters');

echo template_simple ('index.spt', $data);

// projects [ add project ]

// p1 [ add entry ]
// p2 [ add entry ]

/*
echo '<p>Running tests...</p><pre>';

loader_import ('timetracker.Project');

$project = new TimeTrackerProject ();

$id = $project->add (array ('name' => 'Sitellite Content Server'));
var_dump ($id);

print_r (db_fetch ('select * from timetracker_project'));

var_dump ($project->remove ($id));

loader_import ('timetracker.Entry');

$entry = new TimeTrackerEntry ();

$eid = $entry->add (array (
	'users' => array ('admin', 'lux'),
	'project_id' => $id,
	'task_description' => 'Listened to tunes',
	'started' => date ('Y-m-d H:i:s'),
	'duration' => 2.5,
));
var_dump ($eid);

print_r (db_fetch ('select * from timetracker_entry'));
print_r (db_fetch ('select * from timetracker_user_entry'));

print_r ($entry->find (array (
	'project' => $id,
	'users' => array ('admin', 'lux'),
)));
var_dump ($entry->error);

var_dump (db_fetch ('delete from timetracker_entry'));
var_dump (db_fetch ('delete from timetracker_user_entry'));

echo '</pre>';
*/

?>