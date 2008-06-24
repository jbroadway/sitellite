<?php

if (! session_admin ()) {
	page_title ( 'Deadlines - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="deadlines-app" />
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

loader_import ('saf.Date');
loader_import ('saf.Date.Calendar.Simple');

if (! isset ($parameters['simplecal'])) {
	$parameters['simplecal'] = date ('Y-m');
}

$cal = new SimpleCal ($parameters['simplecal']);

$res = db_fetch_array (
	'select id, title, project, type, details, ts from deadlines where ts >= ? and ts <= ?',
	$parameters['simplecal'] . '-01 00:00:00',
	$parameters['simplecal'] . '-' . Date::format ($parameters['simplecal'] . '-01', 't') . ' 23:59:59'
);

$classes = array ();
$colours = array (
	'blue', 'green', 'brown', 'orange', '#a3f', 'red', 'grey', 'maroon', 'dark green', 'dark blue', 'purple', '#222'
);

foreach (array_keys ($res) as $k) {
	$class = preg_replace ('/[^a-zA-Z0-9-]+/', '-', $res[$k]->project);
	$res[$k]->class = $class;
	$cal->addHTML (
		(int) array_pop (explode ('-', array_shift (explode (' ', $res[$k]->ts)))),
		template_simple ('item_short.spt', $res[$k])
	);
	if (! isset ($classes[$class])) {
		$classes[$class] = $colours[count ($classes)];
	}
}

page_title ('Deadlines');

function deadlines_filter_class_name ($class) {
	return str_replace ('-', ' ', $class);
}

echo template_simple ('legend.spt', $classes);

echo $cal->render ();

?>