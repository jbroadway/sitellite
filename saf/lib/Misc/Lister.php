<?php

/**
 * @package Misc
 */
class Lister {
	var $list = array ();
	var $iterator = 0;

	function get ($key) {
		return $this->list[$key];
	}

	function set ($key, $value = false) {
		if ($value === false) {
			$this->list[] = $key;
			return count ($this->list) - 1;
		}
		if (isset ($this->list[$key])) {
			$old = $this->list[$key];
		}
		$this->list[$key] = $value;
		if (isset ($old)) {
			return $old;
		}
		return true;
	}

	function count () {
		return count ($this->list);
	}

	function reset () {
		$this->iterator = 0;
	}

	function iterate () {
		if (isset ($this->list[$this->iterator])) {
			$val = $this->list[$this->iterator];
			$this->iterator++;
			return $val;
		}
		$this->reset ();
		return false;
	}

	function walk ($call) {
		$out = '';
		foreach ($this->list as $key => $value) {
			$val = call_user_func ($call, $this->list[$key]);
			if ($val === false) {
				if (is_array ($call)) {
					$this->error = $call[0]->error;
				}
				return false;
			}
			$out .= $val;
		}
		return $out;
	}
}

/*

// tests

include_once ('../../../saf/init.php');

$loader->import ('saf.Template.Simple');

$simple = new SimpleTemplate;
$lister = new Lister;
$lister->set (array (
	'one' => 'One',
	'two' => 'Two',
));
$lister->set (array (
	'one' => 'Three',
	'two' => 'Four',
));

echo '<pre>';
print_r ($lister);
echo '</pre>';

$simple->setTemplate ("<tr><td>{one}</td><td>{two}</td></tr>\n");
$res = $lister->walk (array (&$simple, 'call'));
if (! $res) {
	echo $lister->error;
} else {
	echo "<table border=\"1\" cellpadding=\"3\">\n";
	echo $res;
	echo '</table>';
}
$simple->setTemplate (); // unset

*/

?>