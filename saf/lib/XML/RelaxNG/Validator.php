<?php

$GLOBALS['loader']->import ('saf.XML.RelaxNG.Parser');
$GLOBALS['loader']->import ('saf.Test');

/**
 * @package XML
 */
class RNGValidator {
	var $doc;
	var $parser;
	var $rules = array ();
	var $nodes;
	var $tags;
	var $parents;
	var $doc;
	var $test;

	var $error;
	var $err_code;
	var $err_byte;
	var $err_line;
	var $err_colnum;

	function RNGValidator () {
		$this->test = new Test (0);
	}

	function getRules ($schema) {
		if (@is_file ($schema)) {
			$schema = join ('', file ($schema));
		}
		$this->parser = new RNGParser;
		$this->rules = $this->parser->parse ($schema);
	}

	// need to do some validation here too, that the node is correctly positioned
	function &getRule ($path) {
		$cur = false;
		preg_match_all ('/^\/([a-zA-Z0-9_-]+)\[([0-9]+)\]/', $path, $matches, PREG_SET_ORDER);
		foreach ($matches as $regs) {
			if ($cur === false) {
				if ($regs[1] == $this->rules->name) { // && $regs[2] == 1) {
					$cur =& $this->rules;
				} else {
					return false;
				}
			} else {
				$f = false;
				foreach ($cur->children as $k => $c) {
					if ($c->name == $regs[1]) { // && $regs[2] == ($k + 1)) {
						$cur =& $cur->children[$k];
						$f = true;
						break;
					}
				}
				if ($f === false) {
					return false;
				}
			}
			$path = str_replace ($regs[0], '', $path);
		}
		return $cur;
	}
}

?>