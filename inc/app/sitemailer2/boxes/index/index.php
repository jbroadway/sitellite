<?php

if (! session_admin ()) {
	page_title ( 'SiteMailer2 - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="sitemailer2-app" />
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

page_title ('SiteMailer');

$data = array (
	'groups' => array (),
	'total' => 0,
);

$res = db_fetch (
	'select * from sitemailer2_category order by name asc'
);
if (! $res) {
	$res = array ();
} elseif (is_object ($res)) {
	$res = array ($res);
}

foreach ($res as $key => $row) {
	$res[$key]->subscribers = db_shift ('select count(*) from sitemailer2_subscriber_category where category = ?', $row->id);
	if (! $res[$key]->subscribers) {
		$res[$key]->subscribers = '&#048;';
	}
}

$data['groups'] = $res;
if (! $data['total']) {
	$data['total'] = '&#048;';
}

$data['total'] = db_shift ('select count(*) from sitemailer2_subscriber');
if (! $data['total']) {
	$data['total'] = '&#048;';
}

echo template_simple ('index.spt', $data);

?>