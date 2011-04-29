<?php

/* Parameters contains:
 * - changelog: Summary of the changes
 * - collection: The collection the item belongs to
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
 * - delete
 */

$default_domain = conf ('Site', 'domain');

$mtime = time ();

$rex = new Rex ($parameters['collection']);
if (! $rex->collection || ! $rex->info['Collection']['sitesearch_url']) {
	return;
}

loader_import ('sitesearch.SiteSearch');

$search = new SiteSearch;

foreach ($parameters['key'] as $key) {
	$search->delete (site_prefix () . '/index/' . sprintf ($rex->info['Collection']['sitesearch_url'], $key));
}

?>