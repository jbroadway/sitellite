<?php

if (! session_admin ()) {
	page_title ( 'Web Files - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post">
		<input type="hidden" name="goto" value="webfiles-app" />
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

if ($parameters['clear'] == 'yes') {
	db_execute ('delete from webfiles_log');
	header ('Location: ' . site_prefix () . '/webfiles-admin-action');
	exit;
}

if (isset ($parameters['type']) && $parameters['type'] != '') {
	$res = db_fetch_array ('select * from webfiles_log where http_status = ? order by id desc', $parameters['type']);
} else {
	$res = db_fetch_array ('select * from webfiles_log order by id desc');
}

$file = @file ('inc/app/webfiles/lib/Server.php');

page_title ('Web Files - Log');

foreach ($res as $k => $row) {
	$start = ($row->line - 7 > 0) ? $row->line - 7 : 0;
	$end   = ($row->line + 7 < count ($file) - 1) ? $row->line + 7 : count ($file) - 1;
	$res[$k]->code = array ();
	for ($i = $start; $i <= $end; $i++) {
		$res[$k]->code[$i] = $file[$i];
	}
}

echo template_simple ('admin.spt', array (
	'log' => $res,
	'type' => $parameters['type'],
	'types' => db_shift_array ('select distinct http_status from webfiles_log order by http_status asc'),
));

?>