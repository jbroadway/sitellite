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
// #198 Allow for HTML mailing templates.

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
 * - modify: Ordinary modifications (source and store)
 * - replace: A change was approved, overwriting the live version
 * - republish: A change was made as a draft, requiring approval
 * - update: Update to a draft that was republished
 */

if ($parameters['collection'] != 'sitefaq_submission') {
	return;
}

switch ($parameters['action']) {
	case 'modify':
		/* For this collection, since it doesn't support versioning,
		 * the action will always be 'modify'.
		 */

		if ($parameters['data']['sitellite_status'] == 'approved') {
			if (conf ('App', 'sitefaq', 'user_anonymity')) {
				$parameters['data']['email'] = db_shift (
					'select email from sitefaq_submission
					where id = ?',
					$parameters['data']['id']
				);
			}

			if (! empty ($parameters['data']['email'])) {
				// reply to user
//START: SEMIAS. #198 Allow for HTML mailing templates.
//-----------------------------------------------
//				@mail (
//					$parameters['data']['email'],
//					intl_get ('FAQ Response'),
//					template_simple ('email_answer.spt', $parameters['data']),
//					'From: faq@' . str_replace ('www.', '', site_domain ())
//				);
//-----------------------------------------------
                site_mail (
					$parameters['data']['email'],
					intl_get ('FAQ Response'),
					template_simple ('email_answer.spt', $parameters['data']),
					'From: faq@' . str_replace ('www.', '', site_domain ()),
                    array ("Is_HTML" => true)
				);
//END: SEMIAS.
			}

			if (conf ('App', 'sitefaq', 'user_anonymity')) {
				// erase user's contact info
				loader_import ('cms.Versioning.Rex');
				$rex = new Rex ('sitefaq_submission');
				$rex->modify (
					$parameters['data'][$rex->key],
					array (
						'name' => '',
						'email' => '',
						'url' => '',
						'ip' => '',
						'member_id' => '',
					)
				);
			}
		}

		break;
	case 'replace':
		break;
	case 'republish':
		break;
	case 'update':
		break;
	default:
		// error
}

?>