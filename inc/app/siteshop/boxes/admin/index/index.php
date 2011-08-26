<?php

// your admin UI begins here

if (! session_admin ()) {
	page_title ('SiteShop 2 - ' . intl_get ('Login'));

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/siteshop-admin-index-action">
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

page_title ('SiteShop 2 - ' . intl_get ('Overview'));

echo loader_box ('siteshop/admin/nav', array ('current' => 'overview'));

echo template_simple ('admin_index.spt', Order::overview ());

?>