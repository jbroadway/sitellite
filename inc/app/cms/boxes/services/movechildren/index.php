<?php

/* Parameters contains:
 * - collection: The collection the item belongs to
 * - key: The primary key value of the item
 * - message: A brief description of the event
 *
 * Note that services are triggered *after* the change has been
 * made.  The only way you can undo changes in a service is by
 * using the cms.Versioning.Rex API if the collection in question
 * supports versioning (not all do).  Also, you can, if necessary,
 * create further modifications to the document, also via the
 * Rex API.
 */

if ($parameters['collection'] == 'sitellite_page') {
	$rex = new Rex ($parameters['collection']);

	$current = $rex->getCurrent ($parameters['key']);
	if (! $current) {
		$current = new StdClass;
		$current->below_page = '';
	}

	foreach (db_shift_array ('select id from sitellite_page where below_page = ?', $parameters['key']) as $child) {
		$method = $rex->determineAction ($child);
		if (! $method) {
			die ($rex->error);
		}
		$rex->{$method} ($child, array ('below_page' => $current->below_page), 'Relocated due to deleted parent page.');
	}
}

?>