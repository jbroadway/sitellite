<?php

if (conf ('Site', 'secure') && (! isset ($_SERVER['HTTPS']) || strtolower ($_SERVER['HTTPS']) != 'on')) {
	header ('Location: https://' . site_domain () . $_SERVER['REQUEST_URI']);
	exit;
}

echo template_simple ('layout/frames.spt', array ('user' => $parameters['username'], 'forward' => $parameters['forward']));
exit;

/*
if (! session_admin ()) {
	page_title (intl_get ('Welcome to Sitellite 4'));

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>' . intl_get ('Invalid login.  Please try again.') . '</p>';
	} else {
		echo '<p>' . intl_get ('Please login to begin your session.') . '</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="cms-app" />
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
				<td><input type="submit" value="{intl Login}" /></td>
			</tr>
		</table>
		</form>

		<p><a href="{site/prefix}/index">{intl Browse your web site.}</a></p>'
	);

	return;
}

page_title (intl_get ('Start by selecting a task or application.'));

echo template_simple ('<table width="100%">
	<tr>
		<td width="50%" valign="top">

<h2>{intl Tasks}</h2>

<ul>
	<li><a href="{site/prefix}/index/cms-add-form">Add a page</a></li>
	<li><a href="{site/prefix}/index">Browse your web site</a></li>
</ul>

		</td>
		<td width="50%" valign="top">

<h2>{intl Applications}</h2>

{filter none}{box cms/admintools}{end filter}

		</td>
	</tr>
</table>');
*/

?>