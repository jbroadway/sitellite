<?php

if (session_is_resource ('delete') && ! session_allowed ('delete', 'rw', 'resource')) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

loader_import ('cms.Versioning.Rex');
loader_import ('cms.Workflow');

echo Workflow::trigger (
	'pre-delete',
	array (
		'collection' => $parameters['_collection'],
		'key' => $parameters['_key'],
		'message' => 'Deleting items (' . join (', ', $parameters['_key']) . ') from "' . $parameters['_collection'] . '" collection.',
	)
);

$rex = new Rex ($parameters['_collection']); // default: database, database

if (isset ($rex->info['Collection']['delete'])) {
	list ($call, $name) = explode (':', $rex->info['Collection']['delete']);
	if ($call == 'box') {
		echo loader_box ($name, $parameters, $context);
	} elseif ($call == 'form') {
		echo loader_form ($name);
	} else {
		echo loader_form ($call);
	}
	return;
}

if (! $parameters['_key']) {
	page_title (intl_get ('No Items Selected'));
	echo '<p><a href="#" onclick="history.go (-1)">' . intl_get ('Back') . '</a></p>';
	return;
}

if (! is_array ($parameters['_key'])) {
	if (! $rex->delete ($parameters['_key'])) {
		page_title (intl_get ('An Error Occurred'));
		echo '<p>' . $rex->error . '</p>';
		return;
	}
	$parameters['_key'] = array ($parameters['_key']);
} else {
	$failed = array ();
	foreach ($parameters['_key'] as $id) {
		if (! $rex->delete ($id)) {
			$failed[] = $id;
		}
	}
	if (count ($failed) > 0) {
		page_title (intl_get ('An Error Occurred'));
		echo '<p>' . $rex->error . '</p>';
		echo '<p>' . intl_get ('The following items were not deleted') . ':</p>';
		echo '<ul>';
		foreach ($failed as $id) {
			echo '<li>' . $id . '</li>';
		}
		echo '</ul>';
		return;
	}
}

echo Workflow::trigger (
	'delete',
	array (
		'collection' => $parameters['_collection'],
		'key' => $parameters['_key'],
		'message' => 'Deleted items (' . join (', ', $parameters['_key']) . ') from "' . $parameters['_collection'] . '" collection.',
	)
);

if (! empty ($parameters['_return']) && $parameters['_return'] != site_prefix () . '/index/' . $parameters['_key'][0] && ! strpos ($parameters['_return'], $parameters['_key'][0])) {
	header ('Location: ' . $parameters['_return']);
	exit;
}

header ('Location: ' . site_prefix () . '/index');
exit;

?>