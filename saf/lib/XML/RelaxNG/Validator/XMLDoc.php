<?php

$GLOBALS['loader']->import ('saf.XML.RelaxNG.Validator');

/**
 * @package XML
 */
//class RNGValidator_Struct {
class RNGValidator_XMLDoc extends RNGValidator {
	function validate (&$doc, $schema) {
		if (count ($this->rules) == 0) {
			$this->getRules ($schema);
		}

		echo 'parsing...<br />';

		$this->error = false;
		if (! $doc->propagateCallback ('_evaluate', false, $this)) {
			$this->error = 'Callback propagation failed';
			return false;
		}
		if (! $doc->write ()) {
			return false;
		}
		echo 'succeeded!<br />';
		return true;
	}

	function _evaluate (&$node, $level = 0) {
		echo '<br />evaluating node ' . $node->name . '...<br />';

//		ob_start ();

		// checking that the node is defined in the schema
		$rule =& $this->getRule ($node->path ());
		if (! is_object ($rule)) { //Checking if a rule exists for the node
			$this->error = 'No rule exists for the node ' . $node->path ();
			return false;
		}

		// checking that the node is valid in its current position

		// checking type
		if (! $rule->validate ($node->content)) { //Verifying type of this node
			$this->error = 'Invalid type for the node ' . $node->path ();
			return false;
		}

		// checking attributes
		foreach ($node->attributes as $key => $attr) {
			if (! isset ($rule->attrs[$key])) { //Checking that the attribute is defined
				$this->error = 'The attribute ' . $node->path () . '/@' . $key . ' is not defined in the schema';
				return false;
			}
/*
			if (! $rule->attrs[$key]->validate ($attr->value)) { //Verifying the validity of the attribute
				$this->error = 'The attribute ' . $node->path () . '/@' . $key . ' is of the wrong type';
				return false;
			}
*/
		}

		// check for missing attributes
		foreach ($rule->attrs as $key => $attr) {
			if (! is_object ($node->attributes[$key])) {
				$this->error = 'The attribute ' . $node->path () . '/@' . $key . ' is missing';
				return false;
			}
		}

/*
		// defined
		if (! isset ($this->rules[ $node->name ])) {
			$this->error = 'No such element defined: ' . $node->name;
			return false;
		}

		// context
		if (! isset ($this->rules[ $node->name ][ $node->parent->name ])) {
			$this->error = 'No such element defined in current context: ' . $node->name;
			return false;
		}

		// rule
		if (
			(
				$this->rules[ $node->name ][ $node->parent->name ]['rule'] == 'required' ||
				$this->rules[ $node->name ][ $node->parent->name ]['rule'] == 'optional'
			) &&
				isset ($this->satisfied[ $node->name . '/' . $node->parent->name ])
		) {
			// make sure there's only one
			$this->error = 'Element already defined and may not be a multiple: ' . $node->name;
			return false;
		}

		// type
		if (count ($this->rules[ $node->name ][ $node->parent->name ]['type']) > 0) {
			$type = $this->rules[ $node->name ][ $node->parent->name ]['type'][0];
			$ns = $this->rules[ $node->name ][ $node->parent->name ]['type'][1];
			if (! $this->checkType ($node->content, $type, $ns)) {
				$this->error = 'Element datatype does not match';
				return false;
			}
		}

		// attributes
		if (count ($this->rules[ $node->name ][ $node->parent->name ]['attrs']) > 0) {
			foreach ($node->attributes as $attr) {
				if (! isset ($this->rules[ $node->name ][ $node->parent->name ]['attrs'][ $attr->name ])) {
					$this->error = 'Element attribute not defined in schema';
					return false;
				}
				// handle missing attributes as well, and datatypes
			}
		}

		// build a list of elements found, so we can check for missing required elements
		if (
			$this->rules[ $node->name ][ $node->parent->name ]['rule'] == 'required' ||
			$this->rules[ $node->name ][ $node->parent->name ]['rule'] == 'optional'
		) {
			if (is_object ($node->parent)) {
				$this->satisfied[$node->name . '/' . $node->parent->name] = $node->parent->path ();
			} else {
				$this->satisfied[$node->name . '/'] = '';
			}
		}

		// check for missing elements
*/

		return true;
	}
}

?>