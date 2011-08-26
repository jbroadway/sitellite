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

loader_box ('sitellite/nav/init');

global $page, $menu;

if (empty ($parameters['position'])) {
	$position = 'left';
} else {
	$position = $parameters['position'];
}

if (session_admin ()) {
	$function = 'session_allowed_sql';
} else {
	$function = 'session_approved_sql';
}

$id = $page->id;
$trail = array ($id);
if (! $menu->{'items_' . $id}->is_section) {
	if (! is_object ($menu->{'items_' . $id}->parent)) {
		$id = false;
	} else {
		while (true) {
			$id = $menu->{'items_' . $id}->parent->id;
			if (in_array ($id, $trail)) {
				$id = false;
				break;
			}
			$trail[] = $id;
			if ($menu->{'items_' . $id}->is_section) {
				break;
			} elseif (! is_object ($menu->{'items_' . $id}->parent)) {
				$id = false;
				break;
			}
		}
	}
}

if ($id) {
	$res = db_fetch ('
		select *
		from sitellite_sidebar
		where position = ?
		and (
			show_on_pages = ? or
			show_on_pages = ? or
			show_on_pages like ? or
			show_on_pages like ? or
			show_on_pages like ?
		)
		and ' . $function () . '
		order by sorting_weight asc
	', $position, 'all', '', $id . '%', '%,' . $id, '%,' . $id . ',%');
} else {
	$res = db_fetch ('
		select *
		from sitellite_sidebar
		where position = ?
		and (
			show_on_pages = ? or
			show_on_pages = ?
		)
		and ' . $function () . '
		order by sorting_weight asc
	', $position, 'all', '');
}

if (! $res) {
	echo '<div class="scm-sidebar-error">' . db_error () . '</div>';
} elseif (is_object ($res)) {
	$res = array ($res);
}

if (session_admin ()) {
	echo '<div class="scm-sidebar-top">';
	echo loader_box (
		'cms/buttons/add',
		array (
			'collection' => 'sitellite_sidebar',
			'return' => site_current (),
			'extra' => '&position=' . $position
		)
	);
	echo '</div>';
}

if (! $res) {
	return;
}

if (intl_lang () != intl_default_lang ()) {
	loader_import ('multilingual.Translation');
	$tr = new Translation ('sitellite_sidebar', intl_lang ());
}

foreach ($res as $key => $row) {
	if (intl_lang () != intl_default_lang ()) {
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

	if (count ($res) == 1) {
		$up = false;
		$down = false;
		$upkey = '';
		$downkey = '';
	} elseif ($key == 0) {
		$up = false;
		$down = true;
		$upkey = '';
		$downkey = $res[1]->id;
	} elseif ($key == count ($res) - 1) {
		$up = true;
		$down = false;
		$upkey = $res[$key - 1]->id;
		$downkey = '';
	} else {
		$up = true;
		$down = true;
		$upkey = $res[$key - 1]->id;
		$downkey = $res[$key + 1]->id;
	}
	//if (session_admin ()) {
	//	echo  '<div class="scm-sidebar scm-status-' . str_replace (array (' ', '_'), array ('-', '-'), $row->sitellite_status) . '" id="scm-sidebar-' . str_replace ('_', '-', $row->id) . '">';
	//} else {
		echo  '<div class="scm-sidebar" id="scm-sidebar-' . str_replace ('_', '-', $row->id) . '">';
	//}

	if (session_admin ()) {
		$GLOBALS['scm_sidebar_body'] = $row->body;
		$buttons = loader_box (
			'cms/buttons',
			array (
				'collection' => 'sitellite_sidebar',
				'id' => $row->id,
				'status' => $row->sitellite_status,
				'access' => $row->sitellite_access,
				'team' => $row->sitellite_team,
				'add' => false,
				'float' => true,
				'up' => $up,
				'down' => $down,
				'upkey' => $upkey,
				'downkey' => $downkey,
				'return' => site_current ()
			)
		);
		$row->body = $GLOBALS['scm_sidebar_body'];
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
			$space = '&nbsp;';
			echo $buttons;
			if (! empty ($row->title)) {
				echo '<h2>' . $row->title . "</h2>\n\n";
			}
			echo '<div class="scm-sidebar-body">' . $out . '</div>';
            echo '<div class="scm-sidebar-bottom" id="scm-sidebar-bottom-' . str_replace ('_', '-', $row->id) . '">' . $space . '</div>';
		}
		echo '</div>';
	} else {
		echo $buttons;
		if (! empty ($row->title)) {
			echo '<h2>' . $row->title . "</h2>\n\n";
		}

		$body = template_parse_body ($row->body);
        $space = '&nbsp;';

		echo '<div class="scm-sidebar-body">' . $body . '</div>';
        echo '<div class="scm-sidebar-bottom" id="scm-sidebar-bottom-' . str_replace ('_', '-', $row->id) . '">' . $space . '</div></div>';
	}
}

?>