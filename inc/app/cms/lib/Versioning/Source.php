<?php

/**
 * This is the base class that you would extend to write Rev/Rex source
 * drivers.  A "source" in Rev/Rex (just Rex from now on) is the location
 * of the files or data themselves, whereas a "store" is where revisions
 * of said data would be stored.  For example, if you wanted to use Rex
 * to track changes to a folder full of files, you would use the Filesystem
 * source driver, and you'd probably choose the Database store driver, which
 * would store changes in the database, but keep the files in their original
 * location.
 *
 * @package CMS
 * @category Versioning
 */

class RevSource {
	/**
	 * Contains any error messages resulting from internal package errors.
	 */
	var $error;

	/**
	 * Constructor method.
	 */
	function RevSource () {
	}

	/**
	 * Sets any properties required by this package to connect to the
	 * data source.
	 *
	 * @param array hash
	 */
	function setProperties ($props) {
		foreach ($props as $key => $value) {
			$this->{$key} = $value;
		}
	}

	/**
	 * Retrieves info about the source.
	 *
	 * @param string
	 * @param array hash
	 * @return array
	 */
	function getInfo ($key, $data) {
	}

	/**
	 * Creates a new item.
	 *
	 * @param string
	 * @param array hash
	 * @return boolean
	 */
	function create ($collection, $data) {
	}

	/**
	 * Modifies an item.
	 *
	 * @param string
	 * @param string
	 * @param mixed
	 * @param array hash
	 * @return boolean
	 */
	function modify ($collection, $key, $id, $data) {
	}

	/**
	 * Deletes an item.
	 *
	 * @param string
	 * @param string
	 * @param mixed
	 * @return boolean
	 */
	function delete ($collection, $key, $id) {
	}

	/**
	 * Gets the specified item.
	 *
	 * @param string
	 * @param string
	 * @param mixed
	 * @return object
	 */
	function getCurrent ($collection, $key, $id) {
	}

	/**
	 * Gets a list of items.
	 *
	 * @param string
	 * @param string
	 * @param array of cms.Versioning.Types objects
	 * @param integer
	 * @param integer
	 * @return array of objects
	 */
	function getList ($collection, $key, $conditions, $limit, $offset, $orderBy, $sort, $count) {
	}

	/**
	 * Returns the structure of the specified collection.  The format of
	 * the results depends on the source type.
	 *
	 * @param string
	 * @return mixed
	 */
	function getStruct ($collection) {
	}
}

?>