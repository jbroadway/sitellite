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
 * - add
 *
 * Action is one of:
 * - null: Document was added
 */

$default_domain = conf ('Site', 'domain');

$mtime = time ();

$rex = new Rex ($parameters['collection']);
if (! $rex->collection || ! $rex->info['Collection']['sitesearch_url']) {
	return;
}

if (! isset ($parameters['data']['sitellite_access'])) {
	$access = 'public';
} else {
	$access = $parameters['data']['sitellite_access'];
}

if (! isset ($parameters['data']['sitellite_status'])) {
	$status = 'approved';
} else {
	$status = $parameters['data']['sitellite_status'];
}

if (! isset ($parameters['data']['sitellite_team'])) {
	$team = 'none';
} else {
	$team = $parameters['data']['sitellite_team'];
}

if (! isset ($rex->info['Collection']['summary_field']) || empty ($parameters['data'][$rex->info['Collection']['summary_field']])) {
	$description = substr (strip_tags ($parameters['data'][$rex->info['Collection']['body_field']]), 0, 128) . '...';
} else {
	$description = $parameters['data'][$rex->info['Collection']['summary_field']];
}

if (! isset ($rex->info['Collection']['keywords_field'])) {
	$keywords = '';
} elseif (strpos ($rex->info['Collection']['keywords_field'], ',') !== false) {
	$op = '';
	foreach (preg_split ('/, ?/', $rex->info['Collection']['keywords_field']) as $f) {
		$keywords .= $op . $parameters['data'][$f];
		$op = ', ';
	}
} else {
	$keywords = $parameters['data'][$rex->info['Collection']['keywords_field']];
}

$data = array (
	'title' => $parameters['data'][$rex->title],
	'url' => site_prefix () . '/index/' . sprintf ($rex->info['Collection']['sitesearch_url'], $parameters['key']),
	'description' => $description,
	'keywords' => $keywords,
	'body' => $parameters['data'][$rex->info['Collection']['body_field']],
	'access' => $access,
	'status' => $status,
	'team' => $team,
	'ctype' => $parameters['collection'],
	'mtime' => (string) $mtime,
	'domain' => $default_domain,
);

if ($parameters['collection'] == 'sitellite_filesystem') {
	loader_import ('sitesearch.Extractor');
	$function = extractor_get_function ('inc/data/' . $parameters['key']);
	if (! $function) {
		$data['body'] = $data['description'];
	} else {
		$new = $function ('inc/data/' . $parameters['key']);
		if (! $new) {
			$data['body'] = $data['description'];
		} else {
			$data['body'] = $new;
		}
	}
}

loader_import ('sitesearch.SiteSearch');

$search = new SiteSearch;

$search->addDocument ($data);

?>