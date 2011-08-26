<?php

$loader->import ('cms.Versioning.Source');

/**
 * @package CMS
 * @category Versioning
 */

class RevSource_Blank extends RevSource {
	function RevSource_Blank () {
	}

	function getInfo ($key, $data) {
		return array ();
	}

	function create ($collection, $data) {
		return true;
	}

	function modify ($collection, $key, $id, $data) {
		return true;
	}

	function delete ($collection, $key, $id) {
		return true;
	}

	function getCurrent ($collection, $key, $id) {
		return true;
	}

	function getList ($collection, $key, $conditions, $limit, $offset, $orderBy, $sort, $count) {
		return array ();
	}

	function getStruct ($collection) {
		return true;
	}
}

?>