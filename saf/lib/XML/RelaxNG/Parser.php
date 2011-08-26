<?php

$loader->import ('saf.XML.RelaxNG.Rule');

/**
 * @package XML
 */
class RNGParser {
	var $rules;
	var $current;
	var $schema;
	var $nodes;
	var $tags;
	var $types = array ('text', 'empty', 'data'); //, 'choice', 'list', 'value');

	var $parser;
	var $encoding;

	var $rule = 'required';

	var $error = false;
	var $err_line;
	var $err_code;
	var $err_byte;
	var $err_colnum;

	function parse ($schema) {
		$this->parser = xml_parser_create ($this->encoding);
		if (! $this->parser) {
			$this->error = 'Relax Error: Failed to create an XML parser!';
			return false;
		}
		if (! xml_parser_set_option ($this->parser, XML_OPTION_CASE_FOLDING, false)) {
			xml_parser_free ($this->parser);
			$this->error = 'Relax Error: Failed to disable case folding!';
			return false;
		}

		$this->schema = $schema;
		//$this->elements = array ();
		//$this->parents = array ();
		//$this->attrs = array ();
		$this->attrOpen = false;
		$this->definitions = array ();
		$this->addUntil = false;
		$this->error = false;
		$this->rule = 'required';

		if (xml_parse_into_struct ($this->parser, $schema, $this->nodes, $this->tags)) {
			xml_parser_free ($this->parser);
			foreach ($this->nodes as $node) {
				$this->{$this->makeMethod ($node['tag'], $node['type'])} ($node);
			}
			return $this->rules;
		} else {
			$this->err_code = xml_get_error_code ($this->parser);
			$this->err_line = xml_get_current_line_number ($this->parser);
			$this->err_byte = xml_get_current_byte_index ($this->parser);
			$this->err_colnum = xml_get_current_column_number ($this->parser);
			$this->error = 'Relax Error: ' . xml_error_string ($this->err_code);
			xml_parser_free ($this->parser);
			return false;
		}
	}

	function makeMethod ($tag, $type) {
		if ($tag == 'element' && $type == 'open') {
			return '_element';
		} elseif ($tag == 'element' && $type == 'close') {
			return '_close_element';
		} elseif ($tag == 'attribute' && $type == 'open') {
			return '_attribute';
		} elseif ($tag == 'attribute' && $type == 'close') {
			return '_close_attribute';
		} elseif ($tag == 'zeroOrMore' && $type == 'open') {
			return '_zeroOrMore';
		} elseif ($tag == 'oneOrMore' && $type == 'open') {
			return '_oneOrMore';
		} elseif ($tag == 'optional' && $type == 'open') {
			return '_optional';
		} elseif ($tag == 'ref') {
			return '_ref';
		} elseif (in_array ($tag, $this->types)) {
			return '_type';
		}
		return '_default';
	}

	function _default ($node) {
		if ($node['tag'] == 'define' && $node['type'] == 'open') { // define tag
			$this->definitions[$node['attributes']['name']] = array ();
			$this->addUntil = $node['level'];
			return;
		} elseif ($node['level'] == $this->addUntil) { // close define tag
			$this->addUntil = false;
			return;
		} elseif ($this->addUntil) { // add to definition
			$this->definitions[array_pop (array_keys ($this->definitions))][] = $node;
			return;
		}
	}

	function _element ($node) {
		if ($this->addUntil) {
			$this->definitions[array_pop (array_keys ($this->definitions))][] = $node;
			return;
		}

		// set default datatypeLibrary value
		if (isset ($node['attributes']['datatypeLibrary'])) {
			$this->typeNS = $node['attributes']['datatypeLibrary'];
			$this->keepTypeNSUntil = $node['level'];
		}

		if (is_object ($this->rules)) {
			$this->current =& $this->current->addChild ($node['attributes']['name'], $this->rule);
		} else {
			$this->rules = new RNGRule ($node['attributes']['name'], $this->rule);
			$this->current =& $this->rules;
		}
		$this->rule = 'required';
	}

	function _close_element ($node) {
		if ($this->addUntil) {
			$this->definitions[ array_pop (array_keys ($this->definitions)) ][] = $node;
			return;
		}

		// keep datatypeLibrary value until appropriate level
		if ($this->keepTypeNSUntil && $node['level'] == $this->typeNS) {
			$this->typeNS = '';
			$this->keepTypeNSUntil = false;
		}

		if (is_object ($this->current->parent)) {
			$this->current =& $this->current->parent;
		} else {
			// we're done, no more elements
			//unset ($this->current);
		}
	}

	function _attribute ($node) {
		if ($this->addUntil) {
			$this->definitions[array_pop (array_keys ($this->definitions))][] = $node;
			return;
		}

		// set default datatypeLibrary value
		if (isset ($node['attributes']['datatypeLibrary'])) {
			$this->typeNS = $node['attributes']['datatypeLibrary'];
			$this->keepTypeNSUntil = $node['level'];
		}

		$this->current->setAttribute ($node['attributes']['name'], $this->rule);
		$this->attrOpen = $node['attributes']['name'];
		$this->rule = 'required';
	}

	function _close_attribute ($node) {
		if ($this->addUntil) {
			$this->definitions[ array_pop (array_keys ($this->definitions)) ][] = $node;
			return;
		}

		// keep datatypeLibrary value until appropriate level
		if ($this->keepTypeNSUntil && $node['level'] == $this->typeNS) {
			$this->typeNS = '';
			$this->keepTypeNSUntil = false;
		}

		$this->attrOpen = false;
	}

	function _zeroOrMore ($node) { // * rule
		if ($this->addUntil) {
			$this->definitions[ array_pop (array_keys ($this->definitions)) ][] = $node;
			return;
		}

		$this->rule = 'zero or more';
	}

	function _oneOrMore ($node) { // + rule
		if ($this->addUntil) {
			$this->definitions[ array_pop (array_keys ($this->definitions)) ][] = $node;
			return;
		}

		$this->rule = 'one or more';
	}

	function _optional ($node) { // ? rule
		if ($this->addUntil) {
			$this->definitions[ array_pop (array_keys ($this->definitions)) ][] = $node;
			return;
		}

		$this->rule = 'optional';
	}

	function _ref ($node) {
		if (is_array ($this->definitions[ $node['attributes']['name'] ]) && count ($this->definitions[ $node['attributes']['name'] ]) > 0) {
			foreach ($this->definitions[ $node['attributes']['name'] ] as $n) {
				$n['level'] = $node['level'] + ($n['level'] - 3);
				$this->{$this->makeMethod ($n['tag'], $n['type'])} ($n);
			}
		}
	}

	function _type ($node) {
		if ($this->addUntil) {
			$this->definitions[ array_pop (array_keys ($this->definitions)) ][] = $node;
			return;
		}

		if ($node['tag'] == 'data') {
			$type = $node['attributes']['type'];
		} else {
			$type = $node['tag'];
		}

		if (isset ($node['attributes']['datatypeLibrary'])) {
			$ns = $node['attributes']['datatypeLibrary'];
		} else {
			$ns = $this->typeNS;
		}

		if ($this->attrOpen) {
			$this->current->setType ($type, $ns, $this->attrOpen);
		} else {
			$this->current->setType ($type, $ns);
		}
	}
}

?>