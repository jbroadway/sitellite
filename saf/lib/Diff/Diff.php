<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// Diff provides a lightweight (fast), simple, and effective means of
// comparing the difference between two strings.
//

// split by newlines
define ('DIFF_LINE', "/(\r\n|\n)/s");

// split by empty space
define ('DIFF_SPACE', "/([\r\n\t ]+)/s");

// what we want here is something that splits each character
//define ('DIFF_CHAR', "//s");

// split by html tags
//define ('DIFF_HTML', "/([\r\n\t ]*<\/?[a-zA-Z0-9_-]+[^>]+>[\r\n\t ]*)/s");
define ('DIFF_HTML', "/(\r\n|\n)/s");

/**
	 * Diff provides a lightweight (fast), simple, and effective means of
	 * comparing the difference between two strings.  Diff bears no code-level
	 * similarity to most of the popular diff algorithm implementations, because
	 * it relies on three simple, built-in PHP functions to accomplish the tough
	 * stuff, being preg_split(), array_diff(), and array_intersect().  This
	 * should make the SAF Diff package much faster than most others.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $diff = new Diff (DIFF_SPACE);
	 * 
	 * $original = 'a b c e h j l m n p';
	 * $new = 'b c d e f j k l m r s t';
	 * 
	 * echo 'Comparison: ' . $original . ' : ' . $new . '<br />';
	 * foreach ($diff->format ($diff->compare ($original, $new)) as $line) {
	 * 	if ($line[0]) {
	 * 		echo $line[0] . '&nbsp;' . htmlentities_compat ($line[1]) . '<br />';
	 * 	} else {
	 * 		echo '&nbsp;&nbsp;' . htmlentities_compat ($line[1]) . '<br />';
	 * 	}
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Diff
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2002-11-19, $Id: Diff.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */
class Diff {
	/**
	 * Contains the regular expression to use to split the
	 * original strings into arrays for comparison via the array_diff()
	 * and array_intersect() functions.  The two preset modes are
	 * defined in the constants DIFF_LINE and DIFF_SPACE, which split
	 * by newline character and by blank space, respectively.
	 * 
	 * @access	public
	 * 
	 */
	var $splitMode;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$splitMode
	 * 
	 */
	function Diff ($splitMode = DIFF_LINE) {
		$this->splitMode = $splitMode;
	}

	/**
	 * Compares two strings and returns a 2-D array of
	 * the strings added, removed, and that are shared between
	 * the two original strings.
	 * 
	 * @access	public
	 * @param	string	$str1
	 * @param	string	$str2
	 * @return	array
	 * 
	 */
	function compare ($str1, $str2) {
		// returns 3 arrays: added, removed, and the intersect of str1 and str2

		if ($this->splitMode == DIFF_HTML) {
			$str1 = preg_replace ('#[\r\n\t ]+<(/?)(a|strong|em)([^>]*)>[\r\n\t ]+#is', ' <\1\2\3> ', $str1);
			$str2 = preg_replace ('#[\r\n\t ]+<(/?)(a|strong|em)([^>]*)>[\r\n\t ]+#is', ' <\1\2\3> ', $str2);

			$str1 = strip_tags ($str1, '<a><strong><em><ul><li><img>');
			$str2 = strip_tags ($str2, '<a><strong><em><ul><li><img>');

			$str1 = trim ($str1);
			$str2 = trim ($str2);

			$str1 = preg_replace ('/(\r\n|\n\r|\r)/s', "\n", $str1);
			$str2 = preg_replace ('/(\r\n|\n\r|\r)/s', "\n", $str2);

			$str1 = preg_replace ('/\n +\n/s', "\n\n", $str1);
			$str2 = preg_replace ('/\n +\n/s', "\n\n", $str2);

			$str1 = preg_replace ('/\n\n+/s', "\n\n", $str1);
			$str2 = preg_replace ('/\n\n+/s', "\n\n", $str2);

			$str1 = preg_replace ('/\n +/s', "\n", $str1);
			$str2 = preg_replace ('/\n +/s', "\n", $str2);

			$str1 = wordwrap ($str1, 80, "\n", false);
			$str2 = wordwrap ($str2, 80, "\n", false);
		}

		$a = preg_split ($this->splitMode, $str1, -1);
		$b = preg_split ($this->splitMode, $str2, -1);
		$removed = array_diff ($a, $b);
		$added = array_diff ($b, $a);
		$intersect = array_intersect ($a, $b);
		return array ($added, $removed, $intersect);
	}

	/**
	 * Accepts the input from compare() either directly or
	 * indirectly, and returns another 2-D array where each element
	 * in the top level array is an array with the first value being
	 * either false, "+" (plus), or "-" (minus), to represent whether
	 * that line should be added or removed from the first original
	 * string to produce the second, and the second value being the
	 * string to add, remove, or keep as-is.  The parameters $a,
	 * $r, and $i stand for "add", "remove", and "intersect".
	 * 
	 * @access	public
	 * @param	array	$a
	 * @param	array	$r
	 * @param	array	$i
	 * @return	array
	 * 
	 */
	function format ($a, $r = false, $i = false) {
		if ($r === false) {
			$r = $a[1];
			$i = $a[2];
			$a = $a[0];
		}
		$out = array ();

/*
		echo html::h2 ('intersecting');
		info ($i);
		echo html::h2 ('added');
		info ($a);
		echo html::h2 ('removed');
		info ($r);
*/

		$incr = 0;

		$top = array ();
		$top[] = array_shift (array_reverse (array_keys ($i)));
		$top[] = array_shift (array_reverse (array_keys ($r)));
		$top[] = array_shift (array_reverse (array_keys ($a)));
		rsort ($top);
		$top = array_shift ($top);

		for ($x = 0; $x <= $top; $x++) {
			if (isset ($a[$x + $incr])) {
				while (isset ($a[$x + $incr])) {
					$out[] = array ('+', $a[$x + $incr]);
					unset ($a[$x + $incr]);
					$incr++;
				}
			}
			if (isset ($r[$x])) {
				$count = 0;
				while (isset ($r[$x + $count])) {
					$out[] = array ('-', $r[$x + $count]);
					unset ($r[$x + $count]);
					$count++;
					$incr--;
				}
			}
			if (isset ($i[$x])) {
				$out[] = array (false, $i[$x]);
			}
		}
		return $out;
	}
}

/*

// Tests...

include_once ('../../init.php');
//loader_import ('saf.Misc.Microbench');
loader_import ('saf.HTML');
//microbench ();

$diff = new Diff (DIFF_SPACE);

$page1 = array ();
$page2 = array ();

$page1[] = 'a b c d e f g';
$page2[] = 'a c d e t t f';

$page1[] = 'a b c d e f g';
$page2[] = 'a t v c t v e t v g';

$page1[] = 'a b c d e f g';
$page2[] = 'a t v d e h f h g h h h h';

$page1[] = 'a b c e h j l m n p';
$page2[] = 'b c d e f j k l m r s t';

$page1[] = 'once upon a time there was a boy named joe and a girl named bob.';
$page2[] = 'once upon a time long long ago, there was a boy named joe and a dragon named joe-bob.';

$page1[] = 'once upon a time there was a boy named joe and a girl named bob.';
$page2[] = 'once upon a time there was a girl named bob.';

echo '<pre>';
foreach ($page1 as $num => $p1) {
	//print_r ($diff->compare ($page1, $page2));
	//print_r ( $diff->format ($diff->compare ($page1, $page2)) );
	echo "\n\nComparison: " . $page1[$num] . ' : ' . $page2[$num] . "\n";
	foreach ( $diff->format ($diff->compare ($page1[$num], $page2[$num])) as $line) {
		if ($line[0]) {
			echo $line[0] . '&nbsp;' . $line[1] . "\n";
		} else {
			echo '&nbsp;&nbsp;' . $line[1] . "\n";
		}
	}
}
echo '</pre>';

//
echo HR;

echo html::h2 ('arr_diff');

//loader_import ('saf.Diff.ArrDiff');

info (preg_split (DIFF_SPACE, $page1[0]));
info (preg_split (DIFF_SPACE, $page2[0]));
info (array_diff (preg_split (DIFF_SPACE, $page1[0]), preg_split (DIFF_SPACE, $page2[0])));
info (array_diff (preg_split (DIFF_SPACE, $page2[0]), preg_split (DIFF_SPACE, $page1[0])));
info (array_intersect (preg_split (DIFF_SPACE, $page1[0]), preg_split (DIFF_SPACE, $page2[0])));
//

//info ($diff->compare ($page1[0], $page2[0]));
//info ($page1);
//info ($page2);
//info (arr_diff (preg_split (DIFF_SPACE, $page1[0]), preg_split (DIFF_SPACE, $page2[0]), true));

//microbench ();
//echo microbench_display ();

*/

?>