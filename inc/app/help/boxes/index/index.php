<?php

if (! session_admin ()) {
	page_title ( 'Help - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="help-app" />
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

if (empty ($parameters['appname'])) {
	// show a list of apps
	loader_import ('help.Help');
	echo template_simple ('nav-index.spt');
	echo '<h1>' . intl_get ('Sitellite Help') . '</h1>';
	echo '<p>' . intl_get ('Please select an application.') . '</p>' . NEWLINE;
	echo '<ul>' . NEWLINE;
	foreach (help_get_apps () as $app => $name) {
		echo TAB . '<li><a href="' . site_prefix () . '/index/help-app?appname=' . $app . '">' . $name . '</a></li>' . NEWLINE;
	}
	echo '</ul>' . NEWLINE;
	return;
}

if (empty ($parameters['lang'])) {
	$parameters['lang'] = 'en';
}

if (! @file_exists ('inc/app/' . $parameters['appname'] . '/docs')) {
	page_title ($parameters['appname']);
	echo '<p>' . intl_get ('No help files exist for this application.') . '</p>';
	return;
}

loader_import ('help.Help');

$data = array ();
$data['appname'] = $parameters['appname'];
$data['lang'] = $parameters['lang'];
$data['helpfile'] = $parameters['helpfile'];

$data['langs'] = help_get_langs ($parameters['appname']);

echo template_simple ('inline-search.spt', $data);

if (empty ($parameters['helpfile']) || strstr ($parameters['helpfile'], '..')) {
	// generate dynamic table of contents

	// get $fullname of app
	$info = @parse_ini_file ('inc/app/' . $parameters['appname'] . '/conf/config.ini.php');
	$fullname = $info['app_name'];
	if (empty ($fullname)) {
		$fullname = ucfirst ($parameters['appname']);
	}

	$pages = help_get_pages ($parameters['appname'], $parameters['lang']);

	if (count ($pages) > 0) {
		$next_id = help_get_id ($pages[0]);
		$body = @join (@file ($pages[0]));
		$next_title = help_get_title ($body, $next_id);
	} else {
		$next_id = false;
		$next_title = false;
	}

	echo template_simple (
		'nav-toc.spt',
		array (
			'appname' => $parameters['appname'],
			'fullname' => $fullname,
			'next_id' => $next_id,
			'next_title' => $next_title,
			'lang' => $parameters['lang'],
		)
	);

	echo '<h1>' . intl_get ($fullname) . ' ' . intl_get ('Help') . '</h1>';
	echo '<h2>' . intl_get ('Table of Contents') . '</h2>';
	echo '<ol>';
	loader_import ('help.Help');
	foreach ($pages as $file) {
		$id = help_get_id ($file);
		if ($id == 'index') {
			continue;
		}
		$body = @join (@file ($file));
		$title = help_get_title ($body, $id);
		echo '<li><a href="' . site_prefix () . '/index/help-app?appname=' . $parameters['appname'] . '&lang=' . $parameters['lang'] . '&helpfile=' . $id . '">' . $title . '</a></li>';
	}
	echo '</ol>';
	return;
}

$out = join ('', file ('inc/app/' . $parameters['appname'] . '/docs/' . $parameters['lang'] . '/' . $parameters['helpfile'] . '.html'));

if (! empty ($parameters['highlight'])) {
	loader_import ('help.Help');

	foreach (help_split_query ($parameters['highlight']) as $item) {
		$out = preg_replace ('/(' . preg_quote ($item, '/') . ')/i', '<span style=\'background-color: #ff0\'>\1</span>', $out);
	}

	$out = '<p style=\'background-color: #ff0; padding: 3px; margin-top: 20px\'><strong>' . intl_get ('Highlighting Search Terms') . '</strong>: ' . htmlentities ($parameters['highlight']) . '</p>' . $out;
}

// build navigation
$pages = help_get_pages ($parameters['appname'], $parameters['lang']);

$previous = help_get_previous ($parameters['appname'], $parameters['lang'], $parameters['helpfile'], $pages);
if ($previous) {
	$data['previous_id'] = $previous['id'];
	$data['previous_title'] = $previous['title'];
} else {
	$data['previous_id'] = false;
}

$next = help_get_next ($parameters['appname'], $parameters['lang'], $parameters['helpfile'], $pages);
if ($next) {
	$data['next_id'] = $next['id'];
	$data['next_title'] = $next['title'];
} else {
	$data['next_id'] = false;
}

echo template_simple ('nav.spt', $data);

echo $out;

?>
