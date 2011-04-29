<?php

/**
 * @package CMS
 * @category Versioning
 */

class RevStore {
var $error;

	function RevStore () {
	}

	function setProperties ($props) {
		foreach ($props as $key => $value) {
			$this->{$key} = $value;
		}
	}
}

?>