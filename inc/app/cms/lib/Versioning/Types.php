<?php

// note: check source drivers for supported types
// rev/rex spec is that unsupported types should
// be ignored if used instead of generating an error

/**
 * @package CMS
 * @category Versioning
 */

class rField {
	var $field;

	function rField ($field) {
		$this->field = $field;
	}
}

/**
 * @package CMS
 * @category Versioning
 */

class rEqual extends rField {
	function rEqual ($field, $value) {
		$this->field = $field;
		$this->value = $value;
	}
}

/**
 * @package CMS
 * @category Versioning
 */

class rNull extends rField {}

/**
 * @package CMS
 * @category Versioning
 */

class rLike extends rField {
	var $_fields = array ();

	function rLike ($field, $value) {
		$this->field = $field;
		$this->value = $value;
	}

	function setFields ($fields) {
		if (is_array ($fields)) {
			$this->_fields = $fields;
		} else {
			$this->_fields[] = $fields;
		}
	}

	function getFields () {
		if (! in_array ($this->field, $this->_fields)) {
			return array_merge (array ($this->field), $this->_fields);
		} else {
			return $this->_fields;
		}
	}
}

/**
 * @package CMS
 * @category Versioning
 */

class rAllowed extends rField {
	function rAllowed () {}

	function allowed () {
		global $sitellite;
		return session_allowed_sql ();
	}
}

/**
 * @package CMS
 * @category Versioning
 */

class rList extends rField {
	function rList ($field, $list) {
		$this->field = $field;
		$args = func_get_args ();
		if (count ($args) > 2) {
			array_shift ($args); // field
			$this->_list = $args;
		} else {
			$this->_list = $list;
		}
	}

	function getList () {
		return $this->_list;
	}
}

/**
 * @package CMS
 * @category Versioning
 */

class rRegex extends rField {
	function rRegex ($field, $value) {
		$this->field = $field;
		$this->value = $value;
	}
}

/**
 * @package CMS
 * @category Versioning
 */

class rRange extends rField {
	function rRange ($field, $from, $to) {
		$this->field = $field;
		$this->from = $from;
		$this->to = $to;
	}
}

/**
 * @package CMS
 * @category Versioning
 */

class rDateRange extends rField {
	function rDateRange ($field, $from, $to) {
		$this->field = $field;
		$this->from = $from;
		$this->to = $to;
	}
}

/**
 * @package CMS
 * @category Versioning
 */

class rLiteral extends rField {
	function rLiteral ($expr) {
		$this->expr = $expr;
	}
}

/**
 * @package CMS
 * @category Versioning
 */

class rSiteSearch extends rField {
	function rSiteSearch ($search) {
		$this->search = $search;
	}
}

?>