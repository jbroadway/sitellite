<?php

global $cgi;

$data = array ();

$content_types = array ();
$GLOBALS['_content_weights'] = array ();

$applications = parse_ini_file ('inc/conf/auth/applications/index.php');

loader_import ('saf.File.Directory');

$files = Dir::find ('*.php', 'inc/app/cms/conf/collections', false);

foreach ($files as $file) {
	if (strstr ($file, '/.')) {
		continue;
	}
	$data = ini_parse ($file);
	$GLOBALS['_content_weights'][$data['Collection']['name']] = $data['Collection']['list_weight'];
	if (! isset ($data['Collection']['visible']) || $data['Collection']['visible'] != false) {
		if (session_is_resource ($data['Collection']['name']) && ! session_allowed ($data['Collection']['name'], 'rw', 'resource')) {
			continue;
		}
		if (isset ($data['Collection']['app']) && isset ($applications[$data['Collection']['app']]) && ! $applications[$data['Collection']['app']]) {
			continue;
		}
		$content_types[$data['Collection']['name']] = intl_get ($data['Collection']['display']);
	}
}

function cms_collection_sort ($a, $b) {
	global $_content_weights, $_content_types;
	if ($a == $b) {
		return 0;
	}
	if (! isset ($_content_weights[$a]) && ! isset ($_content_weights[$b])) {
		return ($_content_types[$a] > $_content_types[$b]) ? 1 : -1;
	}
	return ($_content_weights[$a] > $_content_weights[$b]) ? -1 : 1;
}

$GLOBALS['_content_types'] = $content_types;
uksort ($content_types, 'cms_collection_sort');
foreach ($content_types as $k => $v) {
	$content_types[site_prefix () . '/index/cms-browse-action?collection=' . $k] = $v;
	unset ($content_types[$k]);
}
//asort ($content_types);
$split = ceil (count ($content_types) / 2);
$one = array_slice ($content_types, 0, $split);
$two = array_slice ($content_types, $split);

$data['content_panel'] = array (
	'name' => 'content',
	'caption' => intl_get ('Content'),
	'action' => '',
	'method' => 'get',
	'select' => 'collection',
	'selected' => $cgi->collection,
	'options' => array ($one, $two),
	'icons' => array (
		// hashes w/ href, src, & alt
/*
		array (
			'href' => 'javascript: document.forms[\'content\'].action = \'' . site_prefix () . '/index/cms-browse-action\'; document.forms[\'content\'].submit ()',
			'src' => site_prefix () . '/inc/app/cms/pix/icons/browse.gif',
			'alt' => intl_get ('Browse'),
		),
		array (
			'href' => 'javascript: document.forms[\'content\'].action = \'' . site_prefix () . '/index/cms-add-form?_return=' . site_prefix () . '/index/cms-browse-action' . urlencode ('?collection=' . $cgi->collection . $data['facet_url']) . '\'; document.forms[\'content\'].submit ()',
			'src' => site_prefix () . '/inc/app/cms/pix/icons/add-larger.gif',
			'alt' => intl_get ('Add'),
		),
		array (
			'href' => 'javascript: document.forms[\'content\'].action = \'' . site_prefix () . '/index/cms-deleted-items-action\'; document.forms[\'content\'].submit ()',
			'src' => site_prefix () . '/inc/app/cms/pix/icons/deleted-items.gif',
			'alt' => intl_get ('Deleted'),
		),
*/
	),
);

loader_import ('cms.Versioning.Rex');

$c = 0;
foreach ($one as $k => $v) {
	$ct = str_replace (site_prefix () . '/index/cms-browse-action?collection=', '', $k);
	$r = new Rex ($ct);
	if ($r->info['Collection']['icon']) {
		$icon = site_prefix () . '/' . $r->info['Collection']['icon'];
	} else {
		$icon = site_prefix () . '/inc/app/cms/pix/icons/content-type.gif';
	}
	$data['content_panel']['icons'][] = array (
		'href' => $k,
		'src' => $icon,
		'alt' => $v,
	);
	$c++;
	if ($c >= 3) {
		break;
	}
}

if (session_is_resource ('app_usradm') && ! session_allowed ('app_usradm', 'rw', 'resource')) {
	$data['admin_panel'] = array (
		'name' => 'admin',
		'caption' => intl_get ('Admin'),
		'action' => '#',
		'method' => 'get',
		'select' => 'list',
		'selected' => '',
		'select-extra' => 'disabled="disabled"',
        'disabled' => 'yes',
		'options' => array (
			// key/value
			array (),
			/*'users' => intl_get ('Users'),
			'roles' => intl_get ('Roles'),
			'teams' => intl_get ('Teams'),
			'resources' => intl_get ('Resources'),
			'statuses' => intl_get ('Statuses'),
			'accesslevels' => intl_get ('Access Levels'),
			'prefs' => intl_get ('Preferences'),*/
		),
		'icons' => array (
			// hashes w/ href, src, & alt
			array (
				'href' => '#', //site_prefix () . '/index/usradm-browse-action?list=users',
				'src' => site_prefix () . '/inc/app/cms/pix/icons/users_disabled.gif',
				'alt' => intl_get ('Users'),
			),
			array (
				'href' => '#', //site_prefix () . '/index/usradm-browse-action?list=roles',
				'src' => site_prefix () . '/inc/app/cms/pix/icons/roles_disabled.gif',
				'alt' => intl_get ('Roles'),
			),
			array (
				'href' => '#', //site_prefix () . '/index/usradm-browse-action?list=teams',
				'src' => site_prefix () . '/inc/app/cms/pix/icons/teams_disabled.gif',
				'alt' => intl_get ('Teams'),
			),
		),
	);
	if (! appconf ('panels_show_disabled')) {
		$data['admin_panel']['icons'] = array ();
        $data['admin_panel']['show_disabled'] = 'yes';
	}
} else {
	$data['admin_panel'] = array (
		'name' => 'admin',
		'caption' => intl_get ('Admin'),
		'action' => site_prefix () . '/index/usradm-browse-action',
		'method' => 'get',
		'select' => 'list',
		'selected' => '',
		'select-extra' => 'onchange="this.form.submit ()"',
		'options' => array (
			// key/value
			array (
				site_prefix () . '/index/usradm-browse-action?list=accesslevels' => intl_get ('Access Levels'),
				site_prefix () . '/index/usradm-browse-action?list=log' => intl_get ('Activity Log'),
				site_prefix () . '/index/usradm-applications-action' => intl_get ('Applications'),
				site_prefix () . '/index/usradm-cache-form' => intl_get ('Cache Settings'),
				site_prefix () . '/index/usradm-browse-action?list=prefs' => intl_get ('Preferences'),
				site_prefix () . '/index/usradm-browse-action?list=resources' => intl_get ('Resources'),
			),
			array (
				site_prefix () . '/index/usradm-browse-action?list=roles' => intl_get ('Roles'),
				site_prefix () . '/index/usradm-settings-form' => intl_get ('Site Settings'),
				site_prefix () . '/index/usradm-browse-action?list=statuses' => intl_get ('Statuses'),
				site_prefix () . '/index/usradm-browse-action?list=teams' => intl_get ('Teams'),
				site_prefix () . '/index/usradm-browse-action?list=users' => intl_get ('Users'),
				site_prefix () . '/index/usradm-workflow-action' => intl_get ('Workflow Services'),
			)
		),
		'icons' => array (
			// hashes w/ href, src, & alt
			array (
				'href' => site_prefix () . '/index/usradm-browse-action?list=users',
				'src' => site_prefix () . '/inc/app/cms/pix/icons/users.gif',
				'alt' => intl_get ('Users'),
			),
			array (
				'href' => site_prefix () . '/index/usradm-browse-action?list=roles',
				'src' => site_prefix () . '/inc/app/cms/pix/icons/roles.gif',
				'alt' => intl_get ('Roles'),
			),
			array (
				'href' => site_prefix () . '/index/usradm-browse-action?list=teams',
				'src' => site_prefix () . '/inc/app/cms/pix/icons/teams.gif',
				'alt' => intl_get ('Teams'),
			),
		),
	);
}

$apps = loader_box ('cms/admintools');
$apps = explode (NEWLINE, $apps);
$c = 0;
foreach ($apps as $k => $v) {
	if (empty ($v)) {
		unset ($apps[$k]);
		continue;
	}
	list ($name, $link) = explode (TAB, $v);
	$apps[$link] = intl_get ($name);
	unset ($apps[$k]);

	if ($c == 0) {
		$short = array_shift (explode ('-', $link));
		$first_icon = array (
			'href' => site_prefix () . '/index/' . $link,
			'src' => site_prefix () . '/inc/app/' . $short . '/pix/icon.gif',
			'alt' => intl_get ($name),
		);
		if (! @file_exists ('inc/app/' . $short . '/pix/icon.gif')) {
			$first_icon['src'] = site_prefix () . '/inc/app/cms/pix/default_icon.gif';
		}
	} elseif ($c == 1) {
		$short = array_shift (explode ('-', $link));
		$second_icon = array (
			'href' => site_prefix () . '/index/' . $link,
			'src' => site_prefix () . '/inc/app/' . $short . '/pix/icon.gif',
			'alt' => intl_get ($name),
		);
		if (! @file_exists ('inc/app/' . $short . '/pix/icon.gif')) {
			$second_icon['src'] = site_prefix () . '/inc/app/cms/pix/default_icon.gif';
		}
	}
	$c++;
}
//$apps = array_merge (array ('' => '- ' . intl_get ('Choose') . ' -'), $apps);
$split = ceil (count ($apps) / 2);
$one = array_slice ($apps, 0, $split);
$two = array_slice ($apps, $split);

$data['app_panel'] = array (
	'name' => 'app',
	'caption' => intl_get ('Tools'),
	'action' => '',
	'method' => 'get',
	'select' => 'list',
	'selected' => '',
	'select-extra' => 'onchange="this.form.action = \'' . site_prefix () . '/index/\' + this.options[this.selectedIndex].value; this.form.submit ()"',
	'options' => array ($one, $two),
	'icons' => array (
		// hashes w/ href, src, & alt
		$first_icon,
		$second_icon,
		/*array (
			'href' => site_prefix () . '/index/sitetracker-app',
			'src' => site_prefix () . '/inc/app/sitetracker/pix/icon.gif',
			'alt' => intl_get ('SiteTracker'),
		),
		array (
			'href' => site_prefix () . '/index/sitemailer-app',
			'src' => site_prefix () . '/inc/app/sitemailer/pix/icon.gif',
			'alt' => intl_get ('SiteMailer'),
		),*/
		array (
			'href' => site_prefix () . '/index/cms-admintools-icons-action',
			'src' => site_prefix () . '/inc/app/cms/pix/icons/more.gif',
			'alt' => intl_get ('More'),
		),
		/*array (
			'href' => site_prefix () . '/index/devnotes-app',
			'src' => site_prefix () . '/inc/app/devnotes/pix/icon.gif',
			'alt' => intl_get ('DevNotes'),
		),
		array (
			'href' => site_prefix () . '/index/myadm-app',
			'src' => site_prefix () . '/inc/app/myadm/pix/icon.gif',
			'alt' => intl_get ('Database Manager'),
		),*/
	),
);
if (! appconf ('panels_show_disabled') && count ($apps) < 2) {
	array_pop ($data['app_panel']['icons']);
}

//loader_import ('saf.HTML');

page_add_script (site_prefix () . '/js/dropmenu.js');

template_bind ('/html/body', template_simple ('layout/panels.spt', $data));

echo '<br clear="both" />
<p style="padding-bottom: 60px">&nbsp;</p>';

?>
