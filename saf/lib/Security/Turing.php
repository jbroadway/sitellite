<?php

// Generate a Turing Test image and output it to the browser

if (! extension_loaded ('gd')) {
	define ('SECURITY_TURING_GD_LOADED', false);
	define ('TURING_TEST_GD_LOADED', false);
	return;
} else {
	define ('SECURITY_TURING_GD_LOADED', true);
	define ('TURING_TEST_GD_LOADED', true);
}

/**
 * @package Security
 */
class Security_Turing {
	var $appendToken = 'Set this to something a cracker is not likely to guess';
	var $length = 6;

	function generateImage ($token) {
		$iFont = 5;	// Font ID
		$iSpacing = 2;	// Spacing between characters
		$iDisplacement = 5; // Vertical chracter displacement

		// Establish font metric and image size

		$iCharWidth = ImageFontWidth ($iFont);
		$iCharHeight = ImageFontHeight ($iFont);
		$iWidth = strlen($token) * ($iCharWidth + $iSpacing);
		$iHeight = $iCharHeight + 2 * $iDisplacement;

		// Create the image
	
		$pic = ImageCreate ($iWidth, $iHeight); 

		// Allocate a background and foreground colour

		$col = array (
			'white'		=> ImageColorAllocate ($pic, 255, 255, 255),
			'blue'		=> ImageColorAllocate ($pic, 45, 45, 100),
			'green'		=> ImageColorAllocate ($pic, 45, 100, 45),
			'red'		=> ImageColorAllocate ($pic, 100, 45, 45),
			'purple'	=> ImageColorAllocate ($pic, 100, 45, 100),
			'grey'		=> ImageColorAllocate ($pic, 225, 225, 225),
			'grey2'		=> ImageColorAllocate ($pic, 200, 200, 200),
		);

		for ($x = 0; $x < $iWidth; $x += 2) {
			for ($y = 0; $y < $iHeight; $y += 2) {
				ImageSetPixel ($pic, $x, $y, $col['grey']);
			}
		}

		$iX=1;

		for ($i=0; $i < strlen ($token); $i++) {
			ImageChar ($pic, $iFont - 1, $iX, $iDisplacement - (rand (-$iDisplacement, $iDisplacement)), $token[$i], $col['grey2']);
			$iX += $iCharWidth + $iSpacing;
		}

		$iX=2;
		$c = array ('blue', 'green', 'red', 'purple');

		for ($i=0; $i < strlen ($token); $i++) {
			$colour = $c[rand (0, count ($c) - 1)];
			ImageChar ($pic, $iFont, $iX, $iDisplacement - (rand (-$iDisplacement, $iDisplacement)), $token[$i], $col[$colour]);
			$iX += $iCharWidth + $iSpacing;
		}

		for ($x = 1; $x < $iWidth; $x += 4) {
			for ($y = 1; $y < $iHeight; $y += 4) {
				ImageSetPixel ($pic, $x, $y, $col['white']);
			}
		}

		// Draw some lines

		for ($i = 0; $i < 4; $i++) {
			ImageLine ($pic, 
				rand (0, $iWidth / 2), 
				rand (0, $iHeight / 2), 
				rand ($iWidth / 2, $iWidth), 
				rand ($iHeight / 2, $iHeight),
				$col['white']);
		}

		ob_start();
		if (function_exists ('imagejpeg')) {
			ImageJPEG($pic);
		} elseif (function_exists ('imagepng')) {
			ImagePNG($pic);
		} else {
			ob_end_clean ();
			return false;
		}
		$data = ob_get_contents();
		ob_end_clean();
		ImageDestroy($pic); 

		return $data;
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
			'<img src="data:image/png;base64,' . base64_encode ($this->generateImage ($token)) . '" style="border: #000 1px solid" />',
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

/**
 * This class is deprecated in favour of the above, but preserved to ensure
 * backwards compatibility.
 */
class TuringTest extends Security_Turing {
}

?>