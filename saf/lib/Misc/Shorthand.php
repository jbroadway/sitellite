<?php

/**
 * Converts a shorthand code syntax into PHP code.  The syntax is as follows:
 *
 * Case-insensitive keywords are replaced by their respective operators.  These include:
 *
 * - AND becomes "&&"
 * - NOT becomes "!"
 * - OR becomes "||"
 * - GT becomes ">"
 * - LT becomes "<"
 * - GE becomes ">="
 * - LE becomes "<="
 * - NE becomes "!="
 * - NEQ becomes "!=="
 * - EQ becomes "=="
 * - EQQ becomes "==="
 * - MOD becomes "%"
 * - X becomes "*"
 * - PLUS becomes "+"
 *
 * In addition to this, the following translations happen:
 *
 * - HTML entities for "<" and ">" are converted into their proper symbols
 * - "." is converted to "->"
 * - "_" is converted to "."
 * - "->" with a space on either side of it is converted back into a "."
 * - Object references are converted to $GLOBALS['object']->property
 * - Single- and double-quoted strings are handled as special cases and are not transformed.
 * - [key] array indexes are expanded to their quoted form of ['key']
 *
 * Here are some examples of the conversion process:
 *
 * - <code>foo.bar ('foo.bar')</code> becomes <code>$GLOBALS['foo']->bar ('foo.bar')</code>
 * - <code>not isset (someArray[keyname])</code> becomes <code>! isset ($GLOBALS['someArray']['keyname'])</code>
 * - <code>foo.bar ge bar.foo and not empty (foo.asdf)</code> becomes <code>$GLOBALS['foo']->bar >= $GLOBALS['bar']->foo && ! empty ($GLOBALS['foo']->asdf)</code>
 *
 * Additionally, you can use the replaceGlobals() method to expand object references to refer
 * to an alternate source, or even to add regular expression replacements to the list of
 * existing syntax transformations, for example:
 *
 * <code><?php
 *
 * $sh = new PHPShorthand ();
 * $sh->replaceGlobals ('mypackage->private_register');
 * echo $sh->transform ('foo.bar ()');
 *
 * ? ></code>
 *
 * This will output: <code>$mypackage->private_register['foo']->bar ()</code>
 *
 * @package Misc
 */
class PHPShorthand {
	/**
	 * The list of str_replace()-able tokens within the expression.
	 *
	 */
	var $strReplacements = array (
		'&gt;' => '>',	// convert HTML '>' entity into proper symbol
		'&lt;' => '<',	// convert HTML '<' entity into proper symbol
		'.' => '->',	// convert '.' into '->'
		' ->' => ' .',	// convert ' ->' back into ' .'
		'-> ' => '. ',	// convert '-> ' back into '. '
		' _ ' => ' . ', // convert ' _ ' into ' . '
		' AND ' => ' && ',
		' and ' => ' && ',
		' OR ' => ' || ',
		' or ' => ' || ',
		' GT ' => ' > ',
		' gt ' => ' > ',
		' GE ' => ' >= ',
		' ge ' => ' >= ',
		' LT ' => ' < ',
		' lt ' => ' < ',
		' LE ' => ' <= ',
		' le ' => ' <= ',
		' NE ' => ' != ',
		' ne ' => ' != ',
		' NEQ ' => ' !== ',
		' neq ' => ' !== ',
		' EQ ' => ' == ',
		' eq ' => ' == ',
		' EQQ ' => ' === ',
		' eqq ' => ' === ',
		' MOD ' => ' %% ',
		' mod ' => ' %% ',
		' PLUS ' => ' + ',
		' plus ' => ' + ',
		' X ' => ' * ',
		' x ' => ' * ',
		// see below for NOT => !
	);
	/**
	 * The list of preg_replace()-able tokens within the expression.
	 *
	 */
	var $pregReplacements = array (
		// convert '"somename->' or '"somename[' back into '"somename.'
		'/(\'|")([a-zA-Z0-9_-]+)(->|\[)/' => '\1\2.',

		// convert '->"' back into '".'
		'/(->|\[)(\'|")/' => '.\2',

		// convert '"->' back into '".'
		'/(\'|")(->|\[)/' => '\1.',

		// convert ' somename->' or '(somename['
		// into ' $somevar->' or '($somevar['
		'/([ \(])([a-zA-Z0-9_-]+)(->|\[)/' => '\1$GLOBALS[\'\2\']\3',

		// convert '[somevar]' to '['somevar']'
		'/\[([a-zA-Z0-9_-]+)\]/' => '[\'\1\']',

		// not to !
		'/ ?not /i' => ' ! ',
	);
	/**
	 * The list of quoted parts of the current expression.
	 *
	 */
	var $sprintfVars = array ();

	/**
	 * Replaces the keyword GLOBALS with the specified $newReg in all
	 * regexp transformations.  Alternately, if $replace is provided, it
	 * will add a new regular expression to the list of transformations
	 * to perform.
	 *
	 * @param string
	 * @param string
	 *
	 */
	function replaceGlobals ($newReg, $replace = false) {
		if ($replace === false) {
			foreach ($this->pregReplacements as $key => $value) {
				$this->pregReplacements[$key] = str_replace (
					'GLOBALS', $newReg, $value
				);
			}
		} else {
			$this->pregReplacements[$newReg] = $replace;
		}
	}

	/**
	 * Adds a quoted value to the $sprintfVars list and returns a quoted %s
	 * for temporary insertion into an expression.  This allows vsprintf()
	 * to be used to expand quoted strings from an expression into their
	 * original form, untouched by the transformations being performed
	 * to the expression.
	 *
	 * @access private
	 * @param string
	 * @param string
	 * @return string
	 *
	 */
	function addSprintf ($data, $quote = "'") {
		$this->sprintfVars[] = stripslashes ($data);
		return $quote . '%s' . $quote;
	}

	/**
	 * Transforms the specified expression into valid PHP
	 *
	 * @param string
	 * @return string
	 *
	 */
	function transform ($data) {
		$this->sprintfVars = array ();

		$data = preg_replace (
			array (
				'/\'([^\']+)\'/e',
				'/"([^"]+)"/e',
			),
			array (
				"\$this->addSprintf ('$1')",
				"\$this->addSprintf (\"$1\", '\"')",
			),
			$data
		);
		//echo htmlentities ($data) . BR;
		//echo '<pre>';
		//print_r ($this->sprintfVars);
		//echo '<pre>';
		//exit;

		$data = str_replace (
			array_keys ($this->strReplacements),
			array_values ($this->strReplacements),
			' ' . $data
		);

		$data = preg_replace (
			array_keys ($this->pregReplacements),
			array_values ($this->pregReplacements),
			$data
		);

		$data = vsprintf ($data, $this->sprintfVars);

		return $data;
	}
}

?>
