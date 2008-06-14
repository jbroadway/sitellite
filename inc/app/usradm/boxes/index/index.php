<?php

// just send people to the cms index -- they have no need for this index page
header ('Location: ' . site_prefix () . '/index/cms-app');
exit;

if (! session_admin ()) {
	echo '<h1>User Manager - Login</h1>';

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="usradm-app" />
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

echo '<h1>' . intl_get ('User Manager - Options') . '</h1>' . NEWLINEx2;

echo template_simple ('<ul>
	<li><a href="{site/prefix}/index/usradm-browse-action?list=users">' . intl_get ('Users') . '</a></li>
	<li><a href="{site/prefix}/index/usradm-browse-action?list=roles">' . intl_get ('Roles') . '</a></li>
	<li><a href="{site/prefix}/index/usradm-browse-action?list=teams">' . intl_get ('Teams') . '</a></li>
	<li><a href="{site/prefix}/index/usradm-browse-action?list=resources">' . intl_get ('Resources') . '</a></li>
	<li><a href="{site/prefix}/index/usradm-browse-action?list=statuses">' . intl_get ('Statuses') . '</a></li>
	<li><a href="{site/prefix}/index/usradm-browse-action?list=accesslevels">' . intl_get ('Access Levels') . '</a></li>
	<li><a href="{site/prefix}/index/usradm-browse-action?list=prefs">' . intl_get ('Preferences') . '</a></li>
</ul>');

//exit;

?>