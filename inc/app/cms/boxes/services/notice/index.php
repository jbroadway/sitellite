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
 * - edit
 *
 * Action is one of:
 * - null: Document was added
 * - modify: Ordinary modifications (source and store)
 * - replace: A change was approved, overwriting the live version
 * - republish: A change was made as a draft, requiring approval
 * - update: Update to a draft that was republished
 */

loader_import ('cms.Workspace.Message');
loader_import ('cms.Versioning.Rex');

$msg = new WorkspaceMessage ();
$rex = new Rex ($parameters['collection']);

if ($parameters['data']['sitellite_status'] == 'pending') {
	// find the associated editor(s) and email them
	$users = db_shift_array (
		'select username from sitellite_user where team = ? and role = "editor"',
		$parameters['data']['sitellite_team']
	);
	$url = sprintf (
		'%s/index/cms-edit-form?_collection=%s&_key=%s',
		site_prefix (),
		$parameters['collection'],
		$parameters['key']
	);
	$msg->send (
		intl_get ('Pending Document Notice'),
		template_simple (
			'services_notice_pending.spt',
			array (
				'url' => $url,
				'changelog' => $parameters['changelog'],
				'collection' => $rex->info['Collection']['display'],
				'key' => $parameters['key'],
			)
		),
		$users
	);
} elseif ($parameters['data']['sitellite_status'] == 'rejected') {
	// find the associated writer and email them
	$url = sprintf (
		'%s/index/cms-edit-form?_collection=%s&_key=%s',
		site_prefix (),
		$parameters['collection'],
		$parameters['key']
	);
	$msg->send (
		intl_get ('Rejected Document Notice'),
		template_simple (
			'services_notice_rejected.spt',
			array (
				'url' => $url,
				'changelog' => $parameters['changelog'],
				'collection' => $rex->info['Collection']['display'],
				'key' => $parameters['key'],
			)
		),
		$parameters['data']['sitellite_owner']
	);
}

?>