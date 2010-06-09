<?php

/**
 * @package siteconnector
 */
class SiteConnector_Service_Test extends SiteConnector_Service {
	/**
	 * Says hello to the caller.
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function hello ($name) {
		return 'Hello ' . $name;
	}

	/**
	 * Returns the current server time as a Unix timestamp.
	 *
	 * @access	public
	 * @return	int
	 */
	function ts () {
		return time ();
	}

	/**
	 * Didn't you just hate always being picked last?  This time,
	 * we're picking at random.
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	function lastPick ($list) {
		return $list[mt_rand (0, count ($list) - 1)];
	}

	/**
	 * This method tests error raising.  It will always return an
	 * error message "Something went wrong...".
	 *
	 * @access	public
	 * @return	string
	 */
	function testError () {
		return siteconnector_error ('Something went wrong...');
	}

	function _testPrivate ($name) {
		return 'Pleased to meet you, ' . $name;
	}
}

?>