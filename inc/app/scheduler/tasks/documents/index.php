<?php

// BEGIN CLI KEEPOUT CHECKING
if (php_sapi_name () !== 'cli') {
    // Add these lines to the very top of any file you don't want people to
    // be able to access directly.
    header ('HTTP/1.1 404 Not Found');
    echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
        . "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
        . "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
        . $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
    exit;
}
// END CLI KEEPOUT CHECKING

// documents scheduler task
// Publishes queued documents and archives expired ones.

$states = array (
	'queued' => array (
		'to' => 'approved',
		'sitellite_startdate' => '(sitellite_startdate is not null and sitellite_startdate <= now())',
		'sitellite_expirydate' => '(sitellite_expirydate is null or sitellite_expirydate > sitellite_startdate)',
		'msg' => 'Publishing queued item.',
	),
	'approved' => array (
		'to' => 'archived',
		'sitellite_expirydate' => '(sitellite_expirydate is not null and sitellite_expirydate <= now())',
		'sitellite_startdate' => '',
		'msg' => 'Archiving expired item.',
	),
);

loader_import ('cms.Versioning.Rex');

foreach (Rex::getCollections () as $table) {
	$rex = new Rex ($table);
	if (! $rex->collection) {
		echo 'Collection "' . $table . '" failed to load.  Continuing.' . NEWLINE;
		continue;
	} elseif ($rex->info['Collection']['scheduler_skip']) {
		continue;
	}

	$cols = $rex->getStruct ();
	if (! isset ($cols['sitellite_startdate'])) {
		// no scheduling in this collection
		continue;
	}

	$pkcol = $rex->key;

	foreach ($states as $fromState => $state) {
		$toState = $state['to'];

		$find = array ('sitellite_status' => new rEqual ('sitellite_status', $fromState));
		if (! empty ($state['sitellite_startdate'])) {
			$find['sitellite_startdate'] = new rLiteral ($state['sitellite_startdate']);
		}
		if (! empty ($state['sitellite_expirydate'])) {
			$find['sitellite_expiry'] = new rLiteral ($state['sitellite_expirydate']);
		}
		$list =& $rex->getStoreList ($find);
		if (! $list) {
			// no results
			continue;
		}

		// got the list
		foreach (array_keys ($list) as $k) {
			$record =& $list[$k];
			// set sitellite_status to $toState
			$upd['sitellite_status'] = $toState;
			$action = $rex->determineAction ($record->{$pkcol}, $toState);
			if (! $rex->{$action} ($record->{$pkcol}, $upd, $state['msg'])) {
				echo $table . '/' . $record->{$pkcol} . ' (' . $record->sitellite_status . ', ' . $toState . ') - ' . $rex->error . NEWLINE;
			}
		}
	}
}

?>