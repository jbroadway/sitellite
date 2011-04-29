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
/* Parameters possibly contains:
 * - action: Type of action performed (see below)
 * - changelog: Summary of the changes
 * - collection: The collection the item belongs to
 * - data: The modified document itself
 * - key: The primary key value of the item
 * - message: A brief description of the event
 * - transition: The transition that triggered this service
 *
 * Transition is add, edit, pre-delete, delete, or error.
 * If transition is edit, action is modify, replace, republish or update.
 *
 * Sends a POST to each URL in appconf('cms_webhooks') with the following
 * POST values:
 *
 * - auth: The cms_webhooks_auth value, if available
 * - event: The transition
 * - summary: The message
 * - action: The action, if available
 * - collection: The collection, if available
 * - key: The key, if available
 * - changelog: The changelog, if available
 *
 * If a value is not set, it is not included in the POST data.
 *
 */

if (! isset ($parameters['transition'])) {
	return;
}

if (! isset ($parameters['message'])) {
	$parameters['message'] = '';
}

loader_import ('pear.HTTP.Request');

foreach (explode ("\n", appconf ('cms_webhooks')) as $url) {
	$url = trim ($url);
	if (! empty ($url)) {
		$req =& new HTTP_request ($url);
		$req->setMethod ('POST');
		$req->addPostData ('event', $parameters['transition']);
		$req->addPostData ('summary', $parameters['message']);
		$key = appconf ('cms_webhooks_auth');
		if (! empty ($key)) {
			$req->addPostData ('auth', $key);
		}
		if (isset ($parameters['action'])) {
			$req->addPostData ('action', $parameters['action']);
		}
		if (isset ($parameters['collection'])) {
			$req->addPostData ('collection', $parameters['collection']);
		}
		if (isset ($parameters['key'])) {
			$req->addPostData ('key', $parameters['key']);
		}
		if (isset ($parameters['changelog'])) {
			$req->addPostData ('changelog', $parameters['changelog']);
		}
		$req->sendRequest ();
	}
}

?>