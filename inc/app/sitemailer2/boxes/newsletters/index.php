<?php

if (! session_admin ()) {
	page_title ( 'SiteMailer 2 - Login' );

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

loader_import ('sitemailer2.Filters');

page_title ('SiteMailer 2');

$data = array ();

$res = db_fetch_array (
	'select * from sitemailer2_newsletter order by name asc'
);

foreach (array_keys ($res) as $key) {
	$res[$key]->subscribers = db_shift ('select count(*) from sitemailer2_recipient_in_newsletter where newsletter = ?', $res[$key]->id);
	$res[$key]->last_sent = db_shift ('select date from sitemailer2_message where newsletter = ? and status != "draft" order by date desc limit 1', $res[$key]->id);
	if (! $res[$key]->subscribers) {
		$res[$key]->subscribers = '&#048;';
	}
}

$data['list'] =& $res;

$data['total'] = db_shift ('select count(*) from sitemailer2_recipient');
if (! $data['total']) {
	$data['total'] = '&#048;';
}

global $cgi;

$msg_list = appconf ('msg');

if (isset ($msg_list[$cgi->msg])) {
	page_onload ('alert (\'' . $msg_list[$cgi->msg] . '\')');
}

echo template_simple ('newsletters.spt', $data);

?>