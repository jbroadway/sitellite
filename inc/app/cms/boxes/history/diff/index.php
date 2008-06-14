<?php

loader_import ('cms.Versioning.Rex');
loader_import ('saf.Date');

$rex = new Rex ($parameters['_collection']); // default: database, database

if (! $rex->collection) {
	header ('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}

$revision = $rex->getRevision ($parameters['_key'], $parameters['_rid'], true);

if (! $revision) {
	header ('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}

$current = $rex->getRevision ($parameters['_key'], $parameters['_current'], true);

if (! $current) {
	header ('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}

$info = array (
	'r_autoid' => $revision->sv_autoid,
	'r_author' => $revision->sv_author,
	'r_action' => $revision->sv_action,
	'r_revision' => Date::timestamp ($revision->sv_revision, 'F j, Y - g:ia'),
	'r_changelog' => $revision->sv_changelog,
	'r_current' => $revision->sv_current,
	'r_deleted' => $revision->sv_deleted,
);

unset ($revision->sv_autoid);
unset ($revision->sv_author);
unset ($revision->sv_action);
unset ($revision->sv_revision);
unset ($revision->sv_changelog);
unset ($revision->sv_current);
unset ($revision->sv_deleted);

$cinfo = array (
	'c_autoid' => $current->sv_autoid,
	'c_author' => $current->sv_author,
	'c_action' => $current->sv_action,
	'c_revision' => Date::timestamp ($current->sv_revision, 'F j, Y - g:ia'),
	'c_changelog' => $current->sv_changelog,
	'c_current' => $current->sv_current,
	'c_deleted' => $current->sv_deleted,
);

unset ($current->sv_autoid);
unset ($current->sv_author);
unset ($current->sv_action);
unset ($current->sv_revision);
unset ($current->sv_changelog);
unset ($current->sv_current);
unset ($current->sv_deleted);

$diff = $rex->compare ($parameters['_key'], $parameters['_rid'], $parameters['_current']);

unset ($diff['sv_autoid']);
unset ($diff['sv_author']);
unset ($diff['sv_action']);
unset ($diff['sv_revision']);
unset ($diff['sv_changelog']);
unset ($diff['sv_current']);
unset ($diff['sv_deleted']);

$altered = array ();

foreach ($diff as $key => $value) {
	if (count ($value) == 1) {
		$diff[$key] = '<pre>  ' . $value[0][1] . '</pre>';
	} else {
		// compile comparison
		$new = '<pre>';
		$ec = 0;
		foreach ($value as $k => $v) {
			if (empty ($v[1])) {
				if ($ec == 0) {
					$ec++;
				} else {
					continue;
				}
			} else {
				$ec = 0;
			}
			if (empty ($v[0])) {
				$v[0] = '  ';
			} elseif ($v[0] == '+') {
				if (! in_array ($key, $altered)) {
					$altered[] = $key;
				}
				$v[0] = '<span style="color: #00f; font-weight: bold">+</span> ';
			} elseif ($v[0] == '-') {
				if (! in_array ($key, $altered)) {
					$altered[] = $key;
				}
				$v[0] = '<span style="color: #d00; font-weight: bold">-</span> ';
			}
			$new .= $v[0] . $v[1] . NEWLINE;
		}
		$new .= '</pre>';
		$diff[$key] = $new;
	}
}

//foreach (get_object_vars ($revision) as $k => $v) {
//	$revision->{$k} = array ('revision' => $v, 'current' => $current->{$k});
//}

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

global $cgi;
template_simple_register ('cgi', $cgi);
echo template_simple (
	'view_diff.spt',
	array_merge ($info, $cinfo, array (
		'diff' => $diff,
		'collection' => $rex->info['Collection']['display'],
		'title' => $current->{$rex->info['Collection']['title_field']},
		'altered' => $altered,
	))
);

//info ($diff, true);

?>