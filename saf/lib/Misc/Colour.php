<?php

/**
 * Returns the difference between two colours, measured in percentage
 * of the first (the higher the percentage value, the closer to the
 * first colour it will be).  Percentage is specified as a decimal
 * value.  The colours are 3 or 6 character hex values.
 *
 * @package Misc
 * @param string first colour
 * @param string second colour
 * @param float percent
 * @return string
 */
function colour_diff ($c1, $c2, $percent) {
	$new = '';
	for ($i = 0; $i < strlen ($c1); $i++) {
		$r1 = hexdec ($c1[$i]);
		$r2 = hexdec ($c2[$i]);
		$new .= dechex ( (($r1 - $r2) * $percent) + $r2 );
	}
	return $new;
}

/**
 * Returns an array of colours between the two specified colours.
 * The first and last values are always the two colours themselves.
 *
 * @param string first colour
 * @param string second colour
 * @param int number of divisions to the gradient
 * @return array
 */
function colour_gradient ($c1, $c2, $divisions = 10) {
	$incr = 1 / ($divisions - 1);
	$c = $incr;
	$colours = array ($c2);
	for ($i = 0; $i < ($divisions - 2); $i++) {
		//info ($c);
		$colours[] = colour_diff ($c1, $c2, $c);
		//info ($colours[count ($colours) - 1]);
		$c += $incr;
	}
	$colours[] = $c1;
	return array_reverse ($colours);
}

?>
