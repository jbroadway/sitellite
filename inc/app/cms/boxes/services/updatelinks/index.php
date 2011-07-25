<?php

/* Parameters contains:
 * - action: Type of action performed (see below)
 * - changelog: Summary of the changes
 * - collection: The collection the item belongs to
 * - data: The modified document itself
 * - key: The primary key value of the item
 * - message: A brief description of the event
 * - transition: The transition that triggered this service
 *
 * Note that services are triggered *after* the change has been
 * made.  The only way you can undo changes in a service is by
 * using the cms.Versioning.Rex API if the collection in question
 * supports versioning (not all do).  Also, you can, if necessary,
 * create further modifications to the document, also via the
 * Rex API.
 *
 * Transition is one of:
 * - edit
 *
 * Action is one of:
 * - null: Document was added
 * - modify: Ordinary modifications (source and store)
 * - replace: A change was approved, overwriting the live version
 * - republish: A change was made as a draft, requiring approval
 * - update: Update to a draft that was republished
 */

// note that changes to ID or file name take effect right away, even though
// the rest of the changes to the document require approval.

loader_import ('cms.Versioning.Rex');

if ($parameters['collection'] == 'sitellite_page' && $parameters['key'] != $parameters['data']['id']) {
	$ids = db_shift_array (
		'select id from sitellite_page where body like ?',
		'%/' . $parameters['key'] . '"%'
	);

	$rex = new Rex ('sitellite_page');

	foreach ($ids as $id) {
		/*$c = $rex->getCurrent ($id);
		if (is_object ($c)) {
			$c->body = str_replace ('/' . $parameters['key'] . '"', '/' . $parameters['data']['id'] . '"', $c->body);
			$method = $rex->determineAction ($id, $c->sitellite_status);
			$rex->{$method} ($id, (array) $c, 'A page linked to in this page was renamed, updating link.');
		}*/
		db_execute ('update sitellite_page set body = replace(body, ?, ?) where id = ?', '/' . $parameters['key'] . '"', '/' . $parameters['data']['id'] . '"', $id);
		db_execute ('update sitellite_page_sv set body = replace(body, ?, ?) where id = ? and (sv_current = "yes" or sitellite_status = "parallel")', '/' . $parameters['key'] . '"', '/' . $parameters['data']['id'] . '"', $id);
	}

} elseif ($parameters['collection'] == 'sitellite_filesystem' && $parameters['key'] != $parameters['data']['name']) {
	$ids = db_shift_array (
		'select id from sitellite_page where body like ?',
		'%/' . $parameters['key'] . '"%'
	);

	$rex = new Rex ('sitellite_page');

	foreach ($ids as $id) {
		/*$c = $rex->getCurrent ($id);
		if (is_object ($c)) {
			$c->body = str_replace ('/' . $parameters['key'] . '"', '/' . $parameters['data']['name'] . '"', $c->body);
			$method = $rex->determineAction ($id, $c->sitellite_status);
			$rex->{$method} ($id, (array) $c, 'A file linked to in this page was renamed, updating link.');
		}*/
		db_execute ('update sitellite_page set body = replace(body, ?, ?) where id = ?', '/' . $parameters['key'] . '"', '/' . $parameters['data']['name'] . '"', $id);
		db_execute ('update sitellite_page_sv set body = replace(body, ?, ?) where id = ? and (sv_current = "yes" or sitellite_status = "parallel")', '/' . $parameters['key'] . '"', '/' . $parameters['data']['name'] . '"', $id);
	}

}

?>