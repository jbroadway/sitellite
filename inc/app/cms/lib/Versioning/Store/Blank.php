<?php

$loader->import ('cms.Versioning.Store');

/**
 * @package CMS
 * @category Versioning
 */

class RevStore_Blank extends RevStore {
	function add () {
		return false;
	}
	function deleteAll () {
		return false;
	}
	function getCurrent () {
		return false;
	}
	function getList () {
		return false;
	}
	function getDeleted () {
		return false;
	}
}

?>