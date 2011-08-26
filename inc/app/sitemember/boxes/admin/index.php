<?php

// your admin UI begins here

if (! session_admin ()) {
	page_title (intl_get ('SiteMember - Login'));

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>' . intl_get ('Invalid password.  Please try again.') . '</p>';
	} else {
		echo '<p>' . intl_get ('Please enter your username and password to enter.') . '</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitemember-admin-action">
		<table cellpadding="5" border="0">
			<tr>
				<td>{intl Username}</td>
				<td><input type="text" name="username" /></td>
			</tr>
			<tr>
				<td>{intl Password}</td>
				<td><input type="password" name="password" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="{intl Enter}" /></td>
			</tr>
		</table>
		</form>'
	);

	return;
}

page_title (intl_get ('SiteMember'));

// user stats to go here...

?>