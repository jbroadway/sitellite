<?php

// BEGIN KEEPOUT CHECKING
// Add these lines to the very top of any file you don't want people to
// be able to access directly.
if (! defined ('SAF_VERSION')) {
  header ('HTTP/1.1 404 Not Found');
  echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
		. "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
		. "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
		. $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
  exit;
}
// END KEEPOUT CHECKING

// box logic begins here

global $page, $menu;

if (empty ($parameters['sidebar'])) {
	return;
}

if (session_admin ()) {
	$function = 'session_allowed_sql';
} else {
	$function = 'session_approved_sql';
}

$row = db_single (
	'select * from sitellite_sidebar where id = ?',
	$parameters['sidebar']
);

if (! $row) {
	echo '<div class="scm-sidebar-error">' . db_error () . '</div>';
	return;
}

if (intl_lang () != intl_default_lang ()) {
	loader_import ('multilingual.Translation');
	$tr = new Translation ('sitellite_sidebar', intl_lang ());
	if (session_admin ()) {
		$translated = $tr->get ($row->id);
	} else {
		$translated = $tr->get ($row->id, true);
	}
	if ($translated) {
		foreach ($translated->data as $k => $v) {
			$row->{$k} = $v;
		}
	}
}

echo  '<div class="scm-sidebar" id="scm-sidebar-' . str_replace ('_', '-', $row->id) . '">';

if (session_admin ()) {
	$buttons = loader_box (
		'cms/buttons',
		array (
			'collection' => 'sitellite_sidebar',
			'id' => $row->id,
			'status' => $row->sitellite_status,
			'access' => $row->sitellite_access,
			'team' => $row->sitellite_team,
			'add' => true,
			'float' => true,
			'up' => false,
			'down' => false,
			'upkey' => '',
			'downkey' => '',
			'return' => site_prefix () . '/index/' . $page->id
		)
	);
} else {
	$buttons = '';
}

if (! empty ($row->alias)) {
	if (strstr ($row->alias, '?')) {
		$parts = parse_url ('box://' . site_domain () . '/' . $row->alias);
		$boxname = substr ($parts['path'], 1);
		parse_str ($parts['query'], $params);
	} else {
		$boxname = $row->alias;
		$params = array ();
	}
	$out = loader_box ($boxname, $params, 'sidebar');
	if (! empty ($out)) {
		echo $buttons;
		if (! empty ($row->title)) {
			echo '<h2>' . $row->title . "</h2>\n\n";
		}
		echo '<div class="scm-sidebar-body">' . $out . '</div>';
	}
	echo '</div>';
} else {
	echo $buttons;
	if (! empty ($row->title)) {
		echo '<h2>' . $row->title . "</h2>\n\n";
	}

	$body = template_parse_body ($row->body);

	echo '<div class="scm-sidebar-body">' . $body . '</div></div>';
}

?>