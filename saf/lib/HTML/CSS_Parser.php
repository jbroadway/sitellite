<?php

loader_import ('saf.Parser');

/**
 * Parses CSS data into 3-dimensional associative arrays.  Properly handles
 * multiple declaration blocks for the same element, but will only keep
 * the final value of a property that is declared again, which should not
 * be a problem for most cases.  Also properly handles /* style comments.
 *
 * Example:
 * <code>
 * <?php
 *
 * loader_import ('saf.HTML.CSS_Parser');
 *
 * $css = new CSS_Parser ();
 *
 * $css->parse ('a { text-decoration: none } div.content { line-height: 15px }');
 *
 * info ($css->list['a']);
 *
 * info ($css->getClasses ('div'));
 *
 * ? >
 * </code>
 *
 * @package HTML
 *
 */
class CSS_Parser extends Parser {
	/**
	 * Whether the parser is in a comment or not.
	 *
	 */
	var $comment = false;

	/**
	 * Whether the parser is in a block or not.
	 *
	 */
	var $block = false;

	/**
	 * The structure created from the previous call to parse().
	 *
	 * @access	public
	 */
	var $list = array ();

	/**
	 * Constructor method.
	 *
	 */
	function CSS_Parser () {
		$this->addInternal ('_comment', '/*');
		$this->addInternal ('_comment_end', '*/');
		$this->addInternal ('_block', '{');
		$this->addInternal ('_block_end', '}');

		$this->comment = false;
		$this->block = false;
		$this->list = array ();
	}

	/**
	 * Returns an array of classes found in the CSS data.  Optionally
	 * limits the classes to those that apply to the specified element.
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 *
	 */
	function getClasses ($tag = false) {
		$classes = array ();
		foreach (array_keys ($this->list) as $block) {
			$names = preg_split ('|[\t >]+|', $block, -1, PREG_SPLIT_NO_EMPTY);
			foreach ($names as $name) {
				if (strpos ($name, '.') !== false) {
					// class found
					list ($t, $class) = explode ('.', $name);
					if ($tag) {
						if (empty ($t) || $tag == $t) {
							$classes[] = array_shift (explode (':', $class));
						}
					} else {
						$classes[] = array_shift (explode (':', $class));
					}
				}
			}
		}
		return array_unique ($classes);
	}

	/**
	 * Returns an array of IDs found in the CSS data.  Optionally
	 * limits the IDs to those that apply to the specified element.
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 *
	 */
	function getIDs ($tag = false) {
		$ids = array ();
		foreach (array_keys ($this->list) as $block) {
			$names = preg_split ('|[\t >]+|', $block, -1, PREG_SPLIT_NO_EMPTY);
			foreach ($names as $name) {
				if (strpos ($name, '#') !== false) {
					// ID found
					list ($t, $id) = explode ('#', $name);
					if ($tag) {
						if (empty ($t) || $tag == $t) {
							$ids[] = $id;
						}
					} else {
						$ids[] = $id;
					}
				}
			}
		}
		return array_unique ($ids);
	}

	/**
	 * Returns all of the stylesheet properties of the specified element.
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 *
	 */
	function getStyle ($tag) {
		return $this->list[$tag];
	}

	function _comment ($token, $name) {
		$this->comment = true;
	}

	function _comment_end ($token, $name) {
		$this->comment = false;
	}

	function _default ($token, $name) {
		if ($this->comment) {
			return;
		}
		if (! $this->block) {
			$this->block = trim ($token);
			if (strpos ($this->block, ',') !== false) {
				$this->block = explode (',', $this->block);
				foreach ($this->block as $k => $v) {
					$this->block[$k] = trim ($v);
					if (! isset ($this->list[$this->block[$k]])) {
						$this->list[$this->block[$k]] = array ();
					}
				}
			} else {
				if (! isset ($this->list[$this->block])) {
					$this->list[$this->block] = array ();
				}
			}
		} else {
			$lines = explode (';', trim ($token));
			foreach ($lines as $line) {
				if (strpos ($line, ':') === false) {
					continue;
				}
				list ($name, $value) = explode (':', $line, 2);
				if (is_array ($this->block)) {
					$n = trim ($name);
					$val = trim ($value);
					foreach ($this->block as $k => $v) {
						$this->list[$this->block[$k]][$n] = $val;
					}
				} else {
					$this->list[$this->block][trim ($name)] = trim ($value);
				}
			}
		}
	}

	function _block ($token, $name) {
		if ($this->comment) {
			return;
		}
	}

	function _block_end ($token, $name) {
		if ($this->comment) {
			return;
		}
		$this->block = false;
	}
}

?>