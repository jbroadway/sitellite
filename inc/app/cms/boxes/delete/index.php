<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #173 Collections - deleting items.
//

if (session_is_resource ('delete') && ! session_allowed ('delete', 'rw', 'resource')) {
	header ('Location: ' . site_prefix () . '/index/cms-cpanel-action');
	exit;
}

loader_import ('cms.Versioning.Rex');
loader_import ('cms.Workflow');

// Start: SEMIAS #173 Collections - deleting items.
if(empty($parameters['_key'])) {

	page_title (intl_get ('An Error Occurred'));
	echo '<p>' . intl_get ("No items selected.") . '</p>';

    echo '<p><a href="#" onclick="history.go (-1); return false;">' . intl_get("Go back.") . '</a></p>';
	return;

}
// END: SEMIAS

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
	if (strpos ($rex->key, ',') !== false) {
		$pkeys = preg_split ('/, ?/', $rex->key);
		$pvals = explode ('|', $parameters['_key']);
		$parameters['_key'] = array ();
		for ($i = 0; $i < count ($pkeys); $i++) {
			$parameters['_key'][$pkeys[$i]] = $pvals[$i];
		}
	}
	if (! $rex->delete ($parameters['_key'])) {
		page_title (intl_get ('An Error Occurred'));
		echo '<p>' . $rex->error . '</p>';
		return;
	}
	$parameters['_key'] = array ($parameters['_key']);
} else {
	$failed = array ();
	foreach ($parameters['_key'] as $id) {
		if (strpos ($rex->key, ',') !== false) {
			$pkeys = preg_split ('/, ?/', $rex->key);
			$pvals = explode ('|', $id);
			$id = array ();
			for ($i = 0; $i < count ($pkeys); $i++) {
				$id[$pkeys[$i]] = $pvals[$i];
			}
		}
		if (! $rex->delete ($id)) {
			$failed[] = $id;
		}
	}
    // index page cannot be deleted 
	if ((count ($failed) > 0) || (in_array('index',$id))) {
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

session_set ('sitellite_alert', intl_get ('The items have been deleted.'));

if (! empty ($parameters['_return']) && $parameters['_return'] != site_prefix () . '/index/' . $parameters['_key'][0] && ! strpos ($parameters['_return'], $parameters['_key'][0])) {
	header ('Location: ' . $parameters['_return']);
	exit;
}

header ('Location: ' . site_prefix () . '/index');
exit;

?>