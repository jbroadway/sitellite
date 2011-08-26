<?php

/* Parameters contains:
 * - transition: The transition that triggered this service
 * - message: A summary of the transition that occurred
 *
 * Note that services are triggered *after* the change has been
 * made.  The only way you can undo changes in a service is by
 * using the cms.Versioning.Rex API if the collection in question
 * supports versioning (not all do).  Also, you can, if necessary,
 * create further modifications to the document, also via the
 * Rex API.
 */

if (! isset ($parameters['transition'])) {
	return;
}

if (! isset ($parameters['message'])) {
	$parameters['message'] = '';
}

db_execute (
	'insert into sitellite_log
		(ts, type, user, ip, request, message)
	values
		(now(), ?, ?, ?, ?, ?)',
	$parameters['transition'],
	session_username (),
	$_SERVER['REMOTE_ADDR'],
	$_SERVER['REQUEST_URI'],
	$parameters['message']
);

?>