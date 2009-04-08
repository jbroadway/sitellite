<?php

if (! session_admin ()) {
	page_title ( 'Database Manager - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="myadm-app" />
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

$res = db_fetch ('show tables');

if (! $res) {
	die (db_error ());
} elseif (is_object ($res)) {
	$res = array ($res);
}

page_title ( 'Database Manager - Tables (' . count ($res) . ')' );

$data = array ('tables' => array ());

foreach ($res as $row) {
	$n = $row->{array_shift (array_keys (get_object_vars ($row)))};
	$data['tables'][] = array (
		'name' => $n,
		'count' => db_shift ('select count(*) from ' . $n),
		'is_collection' => @file_exists ('inc/app/cms/conf/collections/' . $n . '.php'),
		'is_version_data' => preg_match ('/_sv$/', $n),
	);
}
echo template_simple ('index.spt', $data);

//exit;

?>