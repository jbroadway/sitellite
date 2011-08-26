<?php

global $cgi;

loader_import ('cms.Versioning.Rex');
loader_import ('saf.Date');

$rex = new Rex ($cgi->_collection);

if (! $rex->collection) {
	header ('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}

$revision = $rex->getRevision ($cgi->_key, $cgi->_rid, true);

if (! $revision) {
	header ('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}

$info = array (
	'sv_autoid' => $revision->sv_autoid,
	'sv_author' => $revision->sv_author,
	'sv_action' => $revision->sv_action,
	'sv_revision' => Date::timestamp ($revision->sv_revision, 'F j, Y - g:ia'),
	'sv_changelog' => $revision->sv_changelog,
	'sv_current' => $revision->sv_current,
	'sv_deleted' => $revision->sv_deleted,
);

unset ($revision->sv_autoid);
unset ($revision->sv_author);
unset ($revision->sv_action);
unset ($revision->sv_revision);
unset ($revision->sv_changelog);
unset ($revision->sv_current);
unset ($revision->sv_deleted);

$GLOBALS['cms_history_view_colnames'] = array ();
global $cms_history_view_colnames;

foreach ($rex->info as $k => $v) {
	if (strpos ($k, 'hint:') === 0) {
		if (isset ($v['alt'])) {
			$cms_history_view_colnames[str_replace ('hint:', '', $k)] = intl_get ($v['alt']);
		}
	}
}

function cms_filter_colname ($name) {
	global $cms_history_view_colnames;
	if (isset ($cms_history_view_colnames[$name])) {
		return $cms_history_view_colnames[$name];
	}
	return ucwords (str_replace ('_', ' ', $name));
}

template_simple_register ('cgi', $cgi);
echo template_simple (
	'view_revision.spt',
	array_merge ($info, array (
		'revision' => $revision,
		'collection' => $rex->info['Collection']['display'],
		'title' => $revision->{$rex->info['Collection']['title_field']},
		'return' => $cgi->_return,
	))
);

?>