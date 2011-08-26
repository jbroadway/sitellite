<?php

$loader->import ('saf.XML.RelaxNG.Type');

/**
 * @package XML
 */
class RNGRule {
	var $name;
	var $parent;
	var $children = array ();
	var $attrs = array ();
	var $type = false;
	var $rule = 'required';

	function RNGRule ($name, $rule = 'required') {
		$this->name = $name;
		$this->rule = $rule;
	}

	function &addChild ($name, $rule = 'required') {
		$child = new RNGRule ($name, $rule);
		$child->parent =& $this;
		$this->children[] =& $child;
		return $child;
	}

	function validate ($value) {
		if (is_object ($this->type)) {
			if ($this->type->validate ($value)) {
				return true;
			}
			return false;
		} else {
			return true;
		}
	}

	function setType ($type, $ns = '', $setOnAttr = false) {
		global $loader;
		if ($loader->import ('saf.XML.RelaxNG.Type.' . ucfirst ($type))) {
			$obj = 'RNGType_' . $type;
			if ($setOnAttr !== false && isset ($this->attrs[$setOnAttr])) {
				$this->attrs[$setOnAttr]['type'] = new $obj ($ns);
			} else {
				$this->type = new $obj ($ns);
			}
		} else {
			if ($setOnAttr !== false && isset ($this->attrs[$setOnAttr])) {
				$this->attrs[$setOnAttr]['type'] = new RNGType ($ns);
			} else {
				$this->type = new RNGType ($ns);
			}
		}
	}

	function setAttribute ($name, $rule) {
		$this->attrs[$name] = array (
			'rule' => $rule,
			'type' => false,
		);
	}

	function display ($level = 0) {
		$padding = str_pad ('', $level * 2);
		echo $padding . $this->name . " (\n";
		echo $padding . '  rule: ' . $this->rule . "\n";
		echo $padding . '  type: ' . str_replace ('rngtype_', '', get_class ($this->type)) . "\n";
		if (count ($this->attrs) > 0) {
			echo $padding . "  attrs: (\n";
			foreach ($this->attrs as $name => $data) {
				echo $padding . '    ' . $name . ' (' . $data['rule'] . ', '
					. str_replace ('rngtype_', '', get_class ($data['type'])) . ")\n";
			}
			echo $padding . "  )\n";
		} else {
			echo $padding . "  attrs: ()\n";
		}
		foreach ($this->children as $key => $child) {
			$this->children[$key]->display ($level + 1);
		}
		echo $padding . ") # $this->name\n\n";
	}
}

?>