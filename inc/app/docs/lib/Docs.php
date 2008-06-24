<?php

loader_import ('saf.File.Directory');

/**
 * This does all the tokenizing and parsing of the PHP files as well as the retrieval of the
 * list of available files.  It's fairly specific to the saf/lib codebase at the moment, but
 * can be pretty easily abstracted.
 *
 * All methods are called statically for now...
 */
class Docs {
	/**
	 * List all packages (aka folders).
	 */
	function packages () {
		$packages = array ();
		$files = Dir::fetch ('saf/lib');
		foreach ($files as $k => $v) {
			if ($v == 'CVS' || $v == '.' || $v == '..' || $v == 'PEAR' || $v == 'Ext') {
				unset ($files[$k]);
				continue;
			}
			if ($files[$k] == 'Functions.php') {
				$packages[] = 'saf.Functions';
			} else {
				$packages[] = 'saf.' . $v;
			}
		}
		return $packages;
	}

	/**
	 * List all classes for a given package.  Returns an array of arrays.
	 * The first level's keys are class names and the second level is just
	 * an ordinary numbered array of sub-classes.
	 *
	 * It only does one level of inheritance, but it doesn't break when
	 * there's more, it just flatten is so that the 2nd level now has a
	 * class at the top level.  Can always fix that later :)
	 */
	function classes ($package) {
		if ($package == 'saf.Functions') {
			return array ();
		}
		$classes = array ();
		$files = Dir::find ('*.php', 'saf/lib/' . str_replace ('saf.', '', $package), true);
		foreach ($files as $file) {
			$src = file_get_contents ($file);
			$tokens = token_get_all ($src);
			foreach ($tokens as $k => $token) {
				if (is_string ($token)) {
					continue;
				}
				if ($token[0] == T_CLASS) {
					if ($tokens[$k + 4][0] == T_EXTENDS) {
						$classes[$tokens[$k + 6][1]][] = $tokens[$k + 2][1];
					} elseif (! isset ($classes[$tokens[$k + 2][1]])) {
						$classes[$tokens[$k + 2][1]] = array ();
					}
				}
			}
		}
		return $classes;
	}

	/**
	 * List all functions for a given package.
	 */
	function functions ($package) {
		if ($package == 'saf.Functions') {
			$files = array ('saf/lib/Functions.php');
		} else {
			$files = Dir::find ('*.php', 'saf/lib/' . str_replace ('saf.', '', $package), true);
		}
		$functions = array ();
		$class = false;
		$class_count = 0;
		$count = 0;
		foreach ($files as $file) {
			$src = file_get_contents ($file);
			$tokens = token_get_all ($src);
			foreach ($tokens as $k => $token) {
				if (is_string ($token)) {
					if (strpos ($token, '{') !== false) {
						$count++;
					} elseif (strpos ($token, '}') !== false) {
						$count--;
						if ($class !== false && $count <= $class_count) {
							$class = false;
							$class_count = 0;
						}
					}
					continue;
				}
				if ($token[0] == T_CLASS) {
					$class = true;
					$class_count = $count;
				}
				if ($token[0] == T_FUNCTION) {
					if (! $class) {
						if ($tokens[$k + 2] == '&') {
							$functions[] = $tokens[$k + 3][1];
						} else {
							$functions[] = $tokens[$k + 2][1];
						}
					}
				}
			}
		}
		return $functions;
	}

	/**
	 * Makes sure the class is in the list (2D array from classes().  This ensures it's not
	 * a faked request, someone prying around.
	 */
	function isClass ($cls, $list) {
		if ($cls == '_functions_') {
			return true;
		}
		foreach ($list as $name => $subs) {
			if ($cls == $name) {
				return true;
			}
			if (count ($subs) > 0) {
				foreach ($subs as $k => $n) {
					if ($cls == $n) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Parses a comment for its goodies.
	 */
	function _getComment ($c) {
		$comment = array ();
		$c = preg_replace ('/^\/\*\*?/', '', $c);
		$c = preg_replace ('/\*\/$/', '', $c);
		$c = preg_replace ('/\n[\t ]*\* ?/', "\n", $c);
		$comment['comment'] = trim ($c);

		// parse <code>
		if (strpos ($comment['comment'], '<code>') !== false) {
			list ($before, $code, $after) = preg_split ('/<\/?code>/s', $comment['comment']);
			$comment['code'] = highlight_string (str_replace ('? >', '?>', $code), true);
			$comment['comment'] = $before . $after;
		} else {
			$comment['code'] = false;
		}

		// parse @tags
		$c = array ();
		foreach (explode ("\n", $comment['comment']) as $line) {
			if (preg_match ('/^@([^ \t]+)[ \t](.+)$/', $line, $regs)) {
				$comment['params'][$regs[1]] = $regs[2];
			} else {
				$c[] = $line;
			}
		}
		$comment['comment'] = join ("\n", $c);

		// add line breaks
		$comment['comment'] = nl2br (htmlentities_compat ($comment['comment']));

		return $comment;
	}

	/**
	 * Parses the token tree for a function's details (works on methods too).  $k is the
	 * position in $tokens of the T_FUNCTION token.
	 */
	function _getFunction ($k, $tokens) {
		$out = new StdClass;
		if ($tokens[$k + 2] == '&') {
			$out->name = $tokens[$k + 3][1];
		} else {
			$out->name = $tokens[$k + 2][1];
		}
		for ($i = $k; $i > $k - 5; $i--) {
			if (is_string ($tokens[$i])) {
				continue;
			}
			if ($tokens[$i][0] == T_DOC_COMMENT) {
				$c = Docs::_getComment ($tokens[$i][1]);
				foreach ($c as $key => $value) {
					$out->{$key} = $value;
				}
				break;
			}
		}
		$out->line = '';
		for ($i = $k + 2; $tokens[$i] != "{"; $i++) {
			if (is_string ($tokens[$i])) {
				$out->line .= $tokens[$i];
			} else {
				$out->line .= $tokens[$i][1];
			}
		}
		return $out;
	}

	/**
	 * Compiles all the data for all the functions in a package.
	 */
	function getFunctions ($package) {
		if ($package == 'saf.Functions') {
			$files = array ('saf/lib/Functions.php');
		} else {
			$files = Dir::find ('*.php', 'saf/lib/' . str_replace ('saf.', '', $package), true);
		}
		$functions = array ();
		$class = false;
		$class_count = 0;
		$count = 0;
		foreach ($files as $file) {
			$src = file_get_contents ($file);
			$tokens = token_get_all ($src);
			foreach ($tokens as $k => $token) {
				if (is_string ($token)) {
					if (strpos ($token, '{') !== false) {
						$count++;
					} elseif (strpos ($token, '}') !== false) {
						$count--;
						if ($class !== false && $count <= $class_count) {
							$class = false;
							$class_count = 0;
						}
					}
					continue;
				}
				if ($token[0] == T_CLASS) {
					$class = true;
					$class_count = $count;
				}
				if ($token[0] == T_FUNCTION) {
					if (! $class) {
						if ($tokens[$k + 2] == '&') {
							$functions[$tokens[$k + 3][1]] = Docs::_getFunction ($k, $tokens);
						} else {
							$functions[$tokens[$k + 2][1]] = Docs::_getFunction ($k, $tokens);
						}
					}
				}
			}
		}
		return $functions;
	}

	/**
	 * Parses the token tree for a class's details.  $k is the position
	 * in $tokens of the T_FUNCTION token.
	 */
	function _getClass ($k, $tokens) {
		$out = new StdClass;
		$out->name = $tokens[$k + 2][1];
		$out->class_extends = false;

		if ($tokens[$k + 4][0] == T_EXTENDS) {
			$out->class_extends = $tokens[$k + 6][1];
		}

		for ($i = $k; $i > $k - 5; $i--) {
			if (is_string ($tokens[$i])) {
				continue;
			}
			if ($tokens[$i][0] == T_DOC_COMMENT) {
				$c = Docs::_getComment ($tokens[$i][1]);
				foreach ($c as $key => $value) {
					$out->{$key} = $value;
				}
				break;
			}
		}
		$out->line = '';
		for ($i = $k + 2; $tokens[$i] != "{"; $i++) {
			if (is_string ($tokens[$i])) {
				$out->line .= $tokens[$i];
			} else {
				$out->line .= $tokens[$i][1];
			}
		}
		return $out;
	}

	/**
	 * Parses the token tree for a class property.  $k is the position
	 * in $tokens of the T_VAR token.
	 */
	function _getVar ($k, $tokens) {
		$out = new StdClass;
		$out->name = $tokens[$k + 2][1];

		for ($i = $k; $i > $k - 5; $i--) {
			if (is_string ($tokens[$i])) {
				continue;
			}
			if ($tokens[$i][0] == T_DOC_COMMENT) {
				$c = Docs::_getComment ($tokens[$i][1]);
				foreach ($c as $key => $value) {
					$out->{$key} = $value;
				}
				break;
			}
		}
		$out->line = '';
		for ($i = $k + 2; $tokens[$i] != ";"; $i++) {
			if (is_string ($tokens[$i])) {
				$out->line .= $tokens[$i];
			} else {
				$out->line .= $tokens[$i][1];
			}
		}
		return $out;
	}


	/**
	 * Compiles all the data for a given class and its properties and methods.
	 */
	function getClass ($package, $cls) {
		if ($package == 'saf.Functions') {
			$files = array ('saf/lib/Functions.php');
		} else {
			$files = Dir::find ('*.php', 'saf/lib/' . str_replace ('saf.', '', $package), true);
		}

		$out = array (
			'name' => $cls,
			'package' => $package,
			'info' => array (),
			'vars' => array (),
			'methods' => array (),
			'params' => array (),
		);

		$class = false;
		$class_name = false;
		$class_count = 0;
		$count = 0;

		foreach ($files as $file) {
			$src = file_get_contents ($file);
			$tokens = token_get_all ($src);
			foreach ($tokens as $k => $token) {
				if (is_string ($token)) {
					if (strpos ($token, '{') !== false) {
						$count++;
					} elseif (strpos ($token, '}') !== false) {
						$count--;
						if ($class !== false && $count <= $class_count) {
							$class = false;
							$class_count = 0;
						}
					}
					continue;
				}
				if ($token[0] == T_CLASS) {
					$class = true;
					$class_count = $count;
					$class_name = $tokens[$k + 2][1];
					if ($class_name == $cls) {
						$info = Docs::_getClass ($k, $tokens);
						$out['extends'] = $info->class_extends;
						$out['comment'] = $info->comment;
						$out['code'] = $info->code;
						$out['params'] = $info->params;
					}
				}
				if ($token[0] == T_VAR && count ($out['methods']) == 0) {
					$out['vars'][$tokens[$k + 2][1]] = Docs::_getVar ($k, $tokens);
				}
				if ($token[0] == T_FUNCTION) {
					if ($class && $class_name == $cls) {
						if ($tokens[$k + 2] == '&') {
							$out['methods'][$tokens[$k + 3][1]] = Docs::_getFunction ($k, $tokens);
						} else {
							$out['methods'][$tokens[$k + 2][1]] = Docs::_getFunction ($k, $tokens);
						}
					}
				}
			}
		}
		return $out;
	}
}

?>