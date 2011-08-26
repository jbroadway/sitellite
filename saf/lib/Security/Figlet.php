<?php

// Generate a Turing Test and output it to the browser using ext.figlet which
// doesn't require the GD PHP extension.

include_once ('saf/lib/Ext/phpfiglet/phpfiglet_class.php');

/**
 * @package Security
 */
class Security_Figlet {
	var $appendToken = 'Set this to something a cracker is not likely to guess';
	var $length = 6;
	var $font = 'ivrit.flf';
	var $error = false;

	function generateImage ($token) {
		$fig = new phpFiglet ();
		if (! $fig->loadFont ('saf/lib/Ext/phpfiglet/fonts/' . $this->font)) {
			$this->error = 'Failed to load font file.';
			return false;
		}
		return $fig->fetch ($token);
	}

	function makeTest () {
		// Generate a six-digit random string

		$token = (string) rand (100000, 999999);
		$token = strtoupper (
			substr (
				strtr (
					md5 ($token),
					'0oO',
					rand (1, 9) . rand (1, 9) . rand (1, 9)
				), 0, $this->length
			)
		);

		// Output form to the user

		return array (
			'<pre style="font-size: 9px; line-height: 9px; border: 1px solid #444; background: #eee; width: 400px; text-align: center; padding: 5px; margin: 0px; margin-bottom: 5px; color: #444; font-family: monospace">' . $this->generateImage ($token) . '</pre>',
			md5 ($token . $this->appendToken),
		);
	}

	function verify ($input, $hash) {
		$token = strtoupper ($input);

		if (md5 ($token . $this->appendToken) === $hash) {
			return true;
		} else {
			return false;
		}
	}
}

?>