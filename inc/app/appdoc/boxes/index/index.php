<?php

if (! session_admin ()) {
	page_title ( 'AppDoc - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="appdoc-app" />
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

page_title (intl_get ('AppDoc'));

echo '<p>';
//echo '<a href="' . site_prefix () . '/index/appdoc-translation-action?appname=GLOBAL">' . intl_get ('Global Translation') . '</a>';
//echo ' &nbsp; &nbsp; ';
echo '<a href="' . site_prefix () . '/index/help-app?appname=appdoc">' . intl_get ('Help') . '</a>';
echo '</p>';

echo '<p>' . intl_get ('Choose an app') . ':</p>';

loader_import ('saf.File.Directory');

$dir = new Dir (getcwd () . '/inc/app');
if (! $dir->handle) {
	die ($dir->error);
}

$apps = array ();
$files = $dir->read_all ();

echo '<ul>' . NEWLINE;
foreach ($files as $file) {
	if (strpos ($file, '.') === 0 || $file == 'CVS') {
		continue;
	} elseif (@is_dir (getcwd () . '/inc/app/' . $file)) {
		// get name
		$info = ini_parse (getcwd () . '/inc/app/' . $file . '/conf/config.ini.php', false);
		if (isset ($info['app_name'])) {
			$name = $info['app_name'];
		} else {
			$name = ucfirst ($file);
		}
		echo '<li><a href="' . site_prefix () . '/index/appdoc-appinfo-action?appname=' . $file . '">' . $name . '</a></li>' . NEWLINE;
	}
}
echo '</ul>';

?>