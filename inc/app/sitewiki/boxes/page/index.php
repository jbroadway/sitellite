<?php

global $cgi;

if (! isset ($parameters['show'])) {
	$parameters['show'] = appconf ('default_page');
}

$level = 0;
if (session_valid ()) {
	$level++;
}
if (session_admin ()) {
	$level++;
}

$res = db_single (
	'select * from sitewiki_page where id = ?',
	$parameters['show']
);

if (! $res) {
	if ($level >= appconf ('default_edit_level')) {
		global $cgi;
		$cgi->page = $parameters['show'];
		echo loader_form ('sitewiki/edit');
	} else {
		echo template_simple ('not_found.spt');
	}
	return;
}

if ($res->view_level > $level && $res->owner != session_username ()) {
	page_title (substr (preg_replace ('/([A-Z])/', ' \1', $parameters['show']), 1));
	echo template_simple ('not_visible.spt', $res);
	return;
}

if (isset ($parameters['rev'])) {
	$rev = db_single (
		'select * from sitewiki_page_sv where id = ? and sv_autoid = ?',
		$parameters['show'],
		$parameters['rev']
	);
	$res->body = $rev->body;
	$res->updated_on = $rev->sv_revision;
	$res->revision = $parameters['rev'];
	$res->editable = false;
	$res->rollback = ($level >= $res->edit_level) ? true : false;
	$res->revisions = db_shift_array (
		'select sv_autoid from sitewiki_page_sv where id = ? order by sv_revision desc',
		$res->id
	);
	$res->back = 0;
	$res->prev = 0;
	$res->forward = 0;
	$res->next = 0;
	foreach ($res->revisions as $rid) {
		if ($rid < $res->revision) {
			$res->back++;
			if ($rid > $res->prev) {
				$res->prev = $rid;
			}
		} elseif ($rid > $res->revision) {
			$res->forward++;
			if ($res->next == 0 || $rid < $res->next) {
				$res->next = $rid;
			}
		}
	}
	if ($res->forward == 0) {
		$res->rollback = false;
		$res->editable = ($level >= $res->edit_level) ? true : false;
	}
} else {
	$res->revision = false;
	$res->editable = ($level >= $res->edit_level) ? true : false;
	$res->back = db_shift (
		'select count(*) from sitewiki_page_sv where id = ?',
		$res->id
	);
	$res->back--;
	$res->prev = db_shift (
		'select sv_autoid from sitewiki_page_sv where id = ? order by sv_autoid desc limit 1, 1',
		$res->id
	);
	$res->forward = 0;
	$res->next = false;
}

$res->linked_from = db_shift_array (
	'select id from sitewiki_page where body like "%' . $res->id . '%" and id != ?', $res->id
);

$res->files = db_fetch_array (
	'select * from sitewiki_file where page_id = ? order by name asc',
	$res->id
);
foreach (array_keys ($res->files) as $k) {
	$res->files[$k]->size = filesize ('inc/app/sitewiki/data/' . $res->id . '_' . $res->files[$k]->id);
}

loader_import ('sitewiki.Filters');
loader_import ('saf.Misc.RPC');

page_title (sitewiki_filter_id ($res->id));

echo template_simple ('page.spt', $res);

echo rpc_init ();

//info ($res, true);

?>