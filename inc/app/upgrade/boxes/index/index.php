<?php

if (! session_admin ()) {
	page_title ( intl_get ('Upgrade Utility - Login') );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>' . intl_get ('Invalid password.  Please try again.') . '</p>';
	} else {
		echo '<p>' . intl_get ('Please enter your username and password to enter.') . '</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="upgrade-app" />
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

page_title (intl_get ('Upgrade Utility'));

if (! isset ($parameters['run'])) {
	echo template_simple ('index.spt', $parameters);
	return;
}

echo loader_box ('upgrade/' . upgrade_box (), $parameters);

?>