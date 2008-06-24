<?php

if (! session_admin ()) {
	page_title ( 'To Do\'s - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/todo-app">
		<input type="hidden" name="pp" value="{pp}" />
		<input type="hidden" name="proj" value="{proj}" />
		<input type="hidden" name="qq" value="{qq}" />
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
		</form>',
		$cgi
	);

	return;
}

page_title ('To Do\'s');

$projs = db_shift_array ('select * from todo_project order by name asc');
foreach ($projs as $k => $v) {
	if (! db_shift ('select count(*) from todo_list where done = "0000-00-00 00:00:00" and project = ?', $v)) {
		unset ($projs[$k]);
	}
}

$ppl = db_shift_array ('select * from todo_person order by name asc');
foreach ($ppl as $k => $v) {
	if (! db_shift ('select count(*) from todo_list where done = "0000-00-00 00:00:00" and person = ?', $v)) {
		unset ($ppl[$k]);
	}
}

global $cgi;

if (empty ($cgi->pp)) {
	//$cgi->pp = $ppl[0];
	if (in_array (session_username (), $ppl)) {
		$cgi->pp = session_username ();
	} else {
		$cgi->pp = '';
	}
}

if (! isset ($cgi->proj)) {
	$cgi->proj = '';
}

if (! isset ($cgi->qq)) {
	$cgi->qq = '';
}

template_simple_register ('cgi', $cgi);
echo template_simple ('selector.spt', array ('ppl' => $ppl, 'projs' => $projs));

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

$todo = db_fetch_array ('select * from todo_list where done = "0000-00-00 00:00:00" ' . $where . ' order by priority desc, todo asc');

$done = db_fetch_array ('select * from todo_list where done != "0000-00-00 00:00:00" ' . $where . ' order by done desc', $cgi->pp);

loader_import ('saf.Date');

function todo_filter_done ($date) {
	return Date::format ($date, 'M d, Y - h:i A');
}

echo template_simple ('list.spt', array ('todo' => $todo, 'done' => $done, 'pp' => $cgi->pp, 'proj' => $cgi->proj));

?>
