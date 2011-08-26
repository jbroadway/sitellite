<?php

/**
 * @package XML
 */
class RNGType {
	var $ns;

	function RNGType ($ns = '') {
		$this->ns = $ns;
	}

	function validate ($value) {
		return true;
	}
}

?>