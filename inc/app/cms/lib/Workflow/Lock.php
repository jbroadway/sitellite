<?php

/**
 * Template for displaying the output of lock_info ().
 */
define ('LOCK_INFO_TEMPLATE', '<h2>{intl Lock Information}</h2>

<p>
	<table border="0" cellpadding="3" cellspacing="1" width="50%">
		<tr>
			<th align="left" width="10%">{intl ID}</th>
			<th align="left" width="35%">{intl User}</th>
			<th align="left" width="55%">{intl Expires}</th>
		</tr>
		<tr>
			<td>{id}</td>
			<td>{user}</td>
			<td>{expires}</td>
		</tr>
	</table>
</p>');

/**
 * Provides a simple locking mechanism for ensuring multiple users don't
 * try to edit the same resource at the same time.
 *
 * <code>
 * <?php
 *
 * loader_import ('cms.Workflow.Lock');
 *
 * lock_init ();
 *
 * if (lock_exists ('sitellite_page', 'index')) {
 *     echo template_simple (LOCK_INFO_TEMPLATE, lock_info ('sitellite_page', 'index'));
 * } else {
 *     lock_add ('sitellite_page', 'index');
 * }
 *
 * // do your business...
 *
 * lock_remove ('sitellite_page', 'index');
 *
 * ? >
 * </code>
 *
 * @package	Workflow
 * @author	John Luxford <john.luxford@gmail.com>
 * @license	http://www.sitellite.org/index/license	GNU GPL License
 * @version	1.0, 2003-10-13, $Id: Lock.php,v 1.2 2008/02/22 16:23:16 lux Exp $
 * @access	public
 *
 */
class Lock {
	/**
	 * Checks whether a lock exists on the current resource, owned
	 * by another user than the current one.
	 */
	function exists ($resource, $key) {
		return db_shift ('select id from sitellite_lock where user != ? and resource = ? and resource_id = ? and expires > now()', session_username (), $resource, $key);
	}

	/**
	 * Retrieves the info about the lock on the specified item.
	 */
	function info ($resource, $key) {
		return db_single ('select * from sitellite_lock where resource = ? and resource_id = ? order by expires desc', $resource, $key);
	}

	/**
	 * Adds a lock to the specified resource, owned by the current user.
	 */
	function add ($resource, $key, $token = '') {
		$timeout = appconf ('lock_timeout');
		if (! $timeout) {
			$timeout = 3600;
		}
		return db_execute ('insert into sitellite_lock (id, user, resource, resource_id, expires, created, modified, token) values (null, ?, ?, ?, date_add(now(), interval ? second), now(), now(), ?)', session_username (), $resource, $key, $timeout, $token);
	}

	/**
	 * Updates a lock's expiry and modification times.
	 */
	function update ($resource, $key) {
		$timeout = appconf ('lock_timeout');
		if (! $timeout) {
			$timeout = 3600;
		}
		return db_execute ('update sitellite_lock set modified = now(), expires = date_add(now(), interval ? second) where resource = ? and resource_id = ?', $timeout, $resource, $key);
	}

	/**
	 * Removes a lock from the specified resource.
	 */
	function remove ($resource, $key) {
		return db_execute ('delete from sitellite_lock where resource = ? and resource_id = ?', $resource, $key);
	}

	/**
	 * Clears all locks held by the current user.
	 */
	function clear () {
		return db_execute ('delete from sitellite_lock where user = ?', session_username ());
	}

	/**
	 * Clears ALL locks.
	 */
	function clearAll () {
		return db_execute ('delete from sitellite_lock');
	}
}

/**
 * Alias for exists() called on a global $lock object.
 */
function lock_exists ($resource, $key) {
	return $GLOBALS['lock']->exists ($resource, $key);
}

/**
 * Alias for info() called on a global $lock object.
 */
function lock_info ($resource, $key) {
	return $GLOBALS['lock']->info ($resource, $key);
}

/**
 * Alias for add() called on a global $lock object.
 */
function lock_add ($resource, $key, $token = '') {
	return $GLOBALS['lock']->add ($resource, $key, $token);
}

/**
 * Alias for update() called on a global $lock object.
 */
function lock_update ($resource, $key) {
	return $GLOBALS['lock']->update ($resource, $key);
}

/**
 * Alias for remove() called on a global $lock object.
 */
function lock_remove ($resource, $key) {
	return $GLOBALS['lock']->remove ($resource, $key);
}

/**
 * Creates a global $lock object, so that the alias functions
 * can be used.
 */
function lock_init () {
	$GLOBALS['lock'] = new Lock;
}

/**
 * Alias for clear() on a global $lock object.
 */
function lock_clear () {
	$GLOBALS['lock']->clear ();
}

/**
 * Alias for clearAll() on a global $lock object.
 */
function lock_clear_all () {
	$GLOBALS['lock']->clearAll ();
}

?>