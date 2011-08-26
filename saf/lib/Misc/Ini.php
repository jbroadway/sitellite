<?php

//include_once ('../../init.php');

/**
 * Class for parsing, transforming, and writing files in the INI format,
 * as defined by the PHP parse_ini_file() function.
 *
 * New in 1.2:
 * - Added ini_write() convenience function.
 * - Added ability for filters to reverse themselves, which is used to
 *   properly write files that have been filtered on reading.
 *
 * @package Misc
 * @version 1.2, 2003-09-29, $Id: Ini.php,v 1.4 2008/02/17 18:55:47 lux Exp $
 */
class Ini {
	var $trues = array ('on', 'On', 'yes', 'Yes', 'true', 'True');
	var $falses = array ('off', 'Off', 'no', 'No', 'false', 'False');

	/**
	 * Constructor method.
	 */
	function Ini () {
		$this->addFilter ('ini_filter_replace_backticks');
	}

	/**
	 * Parses the specified file as an INI file.  Please note that
	 * this method defaults to assuming INI files _do_ have $sections,
	 * whereas PHP's parse_ini_file() function assumes the opposite.
	 *
	 * @param string
	 * @param boolean
	 * @return array hash
	 */
	function parse ($file, $sections = true) {
		if (! @file_exists ($file)) {
			$data = $this->parseStr ($file, $sections);
		} else {
			$data = @parse_ini_file ($file, $sections);
		}
		if (! $data) {
			return array ();
		}
		if (strtolower (get_class ($this)) == 'ini') {
			return $this->filter ($data, $sections);
		}
		return $data;
	}

	function parseStr ($str, $sections = true) {
		$section = null;
		$data = array ();
		if ($tmp = strtok ($str, "\r\n")) {
			do {
				switch ($tmp{0}) {
					case ';':
					case '#':
						break;
					case '[':
						if (! $sections) {
							break;
						}
						$pos = strpos ($tmp, '[');
						$section = substr ($tmp, $pos + 1, strpos ($tmp, ']', $pos) - 1);
						$data[$section] = array ();
						break;
					default:
						$pos = strpos ($tmp, '=');
						if ($pos === false) {
							break;
						}
						$name = trim (substr ($tmp, 0, $pos));
						$value = trim (substr ($tmp, $pos + 1), ' "');

						$quotes = trim (substr ($tmp, $pos + 1), ' ');
						if ($quotes == $value) {
							// no quotes, parse for literal values and constants
							if (in_array ($value, $this->trues)) {
								$value = '1';
							} elseif (in_array ($value, $this->falses)) {
								$value = '';
							} elseif (defined ($value)) {
								$value = constant ($value);
							}
						}

						if ($sections) {
							$data[$section][$name] = $value;
						} else {
							$data[$name] = $value;
						}
						break;
				}
			} while ($tmp = strtok ("\r\n"));
		}
		return $data;
	}

	/**
	 * Adds a filter to the parse() process.
	 *
	 * @param string function name of the filter
	 * @param array hash of keys to limit the filter to
	 */
	function addFilter ($func, $keys = array ()) {
		$this->_filters[] = array ($func, $keys);
	}

	// note: if $sections is true, then there must be no initial pairs
	// outside of a section at the start.
	/**
	 * Filters the specified data through all registered filters.
	 *
	 * @param array hash of parsed INI data
	 * @param boolean
	 * @param boolean
	 * @return array hash of filtered INI data
	 */
	function filter ($data, $sections = true, $reverse = false) {
		foreach ($this->_filters as $filter) {

			if (count ($filter[1]) > 0) { // limited keys specified
				foreach ($filter[1] as $k) {
					if ($sections) {
						foreach ($data as $key => $value) {
							if (in_array ($k, array_keys ($value))) {
								$data[$key][$k] = $filter[0] ($data[$key][$k], $reverse);
							}
						}
					} else {
						if (in_array ($k, array_keys ($data))) {
							$data[$k] = $filter[0] ($data[$k], $reverse);
						}
					}
				}

			} else { // applies to all values
				foreach ($data as $key => $value) {
					if ($sections) {
						foreach ($value as $k => $v) {
							$data[$key][$k] = $filter[0] ($v, $reverse);
						}
					} else {
						$data[$key] = $filter[0] ($value, $reverse);
					}
				}
			}

		}
		return $data;
	}

	/**
	 * Clears the list of registered filters, and adds one filter
	 * back to the list, ini_filter_replace_backticks, which replaces
	 * backtick characters with double-quotes in values.  This
	 * filter allows a second level of quotes to be used within
	 * a single INI value.
	 */
	function clear () {
		$this->_filters = array ();
		$this->addFilter ('ini_filter_replace_backticks');
	}

	/**
	 * Returns the specified INI structure as an XML document.
	 * The root node is an &lt;INI&gt; tag, and subsequent tags
	 * all represent key names from the $struct.
	 *
	 * @param array hash
	 * @return string XML data
	 */
	function toXml ($struct) {
		$out = "<INI>\n";
		foreach ($struct as $key => $value) {
			$out .= "\t<$key>";
			if (is_array ($value)) {
				$out .= "\n";
				foreach ($value as $k => $v) {
					$out .= "\t\t<$k>" . htmlentities ($v) . "</$k>\n";
				}
				$out .= "\t";
			} else {
				$out .= htmlentities ($value);
			}
			$out .= "</$key>\n";
		}
		$out .= "</INI>\n";

		return $out;
	}

	/**
	 * Parses an XML document into an INI $struct, which can then
	 * be saved to a file or processed further.
	 *
	 * @param string XML data or file name
	 * @param boolean is the $data a file
	 * @return array hash
	 */
	function fromXml ($data, $isFile = false) {
		global $loader;
		$loader->import ('saf.XML.Sloppy');
		$sloppy = new SloppyDOM;
		if ($isFile) {
			$data = @join ('', @file ($data));
		}
		$doc = $sloppy->parse ($data);
		if (! $doc) {
			$this->error = $sloppy->error;
			return false;
		}

		$struct = array ();

		foreach ($doc->root->children as $child) {
			if (count ($child->children) > 0) {
				// 2-d
				$struct[$child->name] = array ();
				foreach ($child->children as $item) {
					$struct[$child->name][$item->name] = $item->content;
				}
			} else {
				// single level
				$struct[$child->name] = $child->content;
			}
		}

		return $struct;
	}

	/**
	 * Formats an individual value from an INI file, using quotes if any invalid
	 * characters are present.
	 *
	 * @param string
	 * @return string
	 */
	function writeValue ($val) {
		if (is_bool ($val)) {
			if ($val) {
				return 'On';
			}
			return 'Off';
		} elseif ($val === '0') {
			return 'Off';
		} elseif ($val === '1') {
			return 'On';
		} elseif ($val === '') {
			return 'Off';
		}
		if (preg_match ('/[^a-zA-Z0-9\/\.@<> _-]/', $val)) {
			return '"' . $val . '"';
		}
		//if (in_array ($val, $this->trues) || in_array ($val, $this->falses)) {
		//	return '"' . $val . '"';
		//}
		return $val;
	}

	/**
	 * Turns an INI structure into an INI formatted string, ready for writing
	 * to a file.
	 *
	 * @param array hash
	 * @param string instructions to include as comments in the data
	 * @return string
	 */
	function write ($struct, $instructions = false) {
		$out = "; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS\n;\n";
		if ($instructions) {
			foreach (preg_split ('/\n/s', $instructions) as $line) {
				$out .= '; ' . $line . "\n";
			}
			$out .= ";\n\n";
		} else {
			$out .= "; WARNING: This file was automatically generated, and it may\n";
			$out .= "; not be wise to edit it by hand.  If there is an interface\n";
			$out .= "; to modify files of this type, please use that interface\n";
			$out .= "; instead of manually editing this file.  If you are not sure\n";
			$out .= "; or are not aware of such an interface, please talk to your\n";
			$out .= "; Sitellite administrator first.\n";
			$out .= ";\n\n";
		}

		if (is_array ($struct[array_shift (array_keys ($struct))])) {
			$sections = true;
		} else {
			$sections = false;
		}

		// reverse-filter
		$struct = $this->filter ($struct, $sections, true);

		foreach ($struct as $key => $value) {
			if (is_array ($value)) {
				$out .= "[$key]\n\n";
				foreach ($value as $k => $v) {
					$out .= str_pad ($k, 24) . '= ' . Ini::writeValue ($v) . "\n\n";
				}
			} else {
				$out .= str_pad ($key, 24) . '= ' . Ini::writeValue ($value) . "\n\n";
			}
		}

		$out .= ";\n; THE END\n;\n; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ " . CLOSE_TAG;

		return $out;
	}
}

// convenience functions:

/**
 * Parses an INI file -- calls parse() on a global $ini object.
 *
 * @param string
 * @param boolean
 * @return array hash
 */
function ini_parse ($file, $sections = true) {
	return $GLOBALS['ini']->parse ($file, $sections);
}

/**
 * Adds a filter to the global $ini object.  Alias of addFilter() method.
 *
 * @param string
 * @param array
 */
function ini_add_filter ($func, $keys = array ()) {
	return $GLOBALS['ini']->addFilter ($func, $keys);
}

/**
 * Filters the specified data using the global $ini object.
 * Alias of filter() method.
 *
 * @param array hash
 * @param boolean
 * @return array hash
 */
function ini_filter ($data, $sections = true) {
	return $GLOBALS['ini']->filter ($data, $sections);
}

/**
 * Clears the list of filters in the global $ini object.
 * Alias of clear() method.
 */
function ini_clear () {
	return $GLOBALS['ini']->clear ();
}

/**
 * Writes an INI data string from the specified structure.
 * Alias of write() in global $ini object.
 *
 * @param array hash
 * @param string
 * @return string
 */
function ini_write ($struct, $instructions = false) {
	return $GLOBALS['ini']->write ($struct, $instructions);
}

// built-in event handlers:

/**
 * Backtick INI filter.  Replaces backticks (`) with double-quotes (").
 * Please note that this filter is on by default in the Ini class.
 *
 * The $reverse parameters tells the filter to do the reverse of its
 * ordinary function and return that instead.  This is used by the
 * write() method to write back an INI file correctly that has
 * been filtered.
 *
 * @param string
 * @param boolean
 * @return string
 */
function ini_filter_replace_backticks ($value, $reverse = false) {
	if ($reverse) {
		return str_replace ('"', '`', $value);
	}
	return str_replace ('`', '"', $value);
}

/**
 * Comma-splitting INI filter.  Breaks a string into a list by the
 * commas in it.  Note: An optional single space is allowed following
 * commas, and will be trimmed.
 *
 * The $reverse parameters tells the filter to do the reverse of its
 * ordinary function and return that instead.  This is used by the
 * write() method to write back an INI file correctly that has
 * been filtered.
 *
 * @param string
 * @param boolean
 * @return array
 */
function ini_filter_split_commas ($value, $reverse = false) {
	if ($reverse) {
		if (is_array ($value)) {
			return join (', ', $value);
		}
		return $value;
	}
	return preg_split ('/, ?/', $value);
}

/**
 * Single-comma-splitting INI filter.  Breaks a string into two by
 * the first comma in it.  Note: An optional single space is allowed
 * following the comma, and will be trimmed.
 *
 * The $reverse parameters tells the filter to do the reverse of its
 * ordinary function and return that instead.  This is used by the
 * write() method to write back an INI file correctly that has
 * been filtered.
 *
 * @param string
 * @param boolean
 * @return array
 */
function ini_filter_split_comma_single ($value, $reverse = false) {
	if ($reverse) {
		if (is_array ($value)) {
			return join (', ', $value);
		}
		return $value;
	}
	return preg_split ('/, ?/', $value, 2);
}

/* tests

echo '<pre>';
$struct = Ini::parse ('../../../inc/boxes/syndicate/settings.php');
var_dump ($struct);
echo "\n\n";
echo htmlentities (Ini::toXml ($struct));
echo "\n\n";
echo htmlentities (Ini::write ($struct, "Instructions about\nthis file..."));
echo "\n\n";
echo htmlentities (Ini::write ($struct));
echo "\n\n";
echo htmlentities (Ini::write (Ini::fromXml (Ini::toXml ($struct))));
echo "\n\n";
echo htmlentities (Ini::write (Ini::fromXml (join ('', file ('../../../sitellite/mod/fortune/fortune.mod')))));
echo '</pre>';

*/

?>