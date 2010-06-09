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
// Alt is a tiny class that alternates through a list of values each time
// you call the next () method.
//

/**
	 * Alt is a tiny class that alternates through a list of values
	 * each time you call the next () method.  When it gets to the end of the
	 * list, the pointer is reset to the beginning.
	 * 
	 * This class is a little redundant, as a simple array would usually
	 * suffice, but the Object Oriented syntax is nicer, and our original
	 * inspiration for this class was a two element array of background
	 * colours for table rows.  The equivalent using an array still required
	 * an if statement, which this eliminates.
	 * 
	 * New in 1.2:
	 * - Added a reset() method.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $c = new Alt ('odd', 'even');
	 * 
	 * while (...) {
	 * 	echo $c->next ();
	 * }
	 * 
	 * --
	 * 
	 * The equivalent using a single variable:
	 * 
	 * $c = 'odd';
	 * 
	 * while (...) {
	 * 	echo $c;
	 * 	if ($c == 'odd') {
	 * 		$c = 'even';
	 * 	} else {
	 * 		$c = 'odd';
	 * 	}
	 * }
	 * 
	 * --
	 * 
	 * The equivalent using an array:
	 * 
	 * $c = array ('odd', 'even');
	 * $i = 0;
	 * 
	 * while (...) {
	 * 	echo $c[$i];
	 * 	if ($i >= count ($c)) {
	 * 		$i = 0;
	 * 	}
	 * }
	 * 
	 * Using arrays, the next(), current(), each(), end(), etc. functions are
	 * not appropriate when the alternating values are not the focus of the
	 * loop, but are still required to change.
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	Misc
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2002-11-13, $Id: Alt.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class Alt {
	/**
	 * List of values to alternate between.
	 * 
	 * @access	public
	 * 
	 */
	var $vals;

	/**
	 * The internal position counter.
	 * 
	 * @access	private
	 * 
	 */
	var $counter;

	/**
	 * Always keeps the value of the current array position here.
	 * 
	 * @access	private
	 * 
	 */
	var $current;

	/**
	 * Constructor method.  If the first parameter is an array,
	 * it is used to set the $vals property.  If not, up to three parameters
	 * can be passed, which are fed into the $vals property array.
	 * 
	 * @access	public
	 * @param	mixed	$vals
	 * @param	mixed	$val2
	 * @param	mixed	$val3
	 * 
	 */
	function Alt ($vals, $val2 = '', $val3 = '') {
		if (is_array ($vals)) {
			$this->vals = $vals;
		} else {
			$this->vals = array ($vals);
			if (! empty ($val2)) {
				array_push ($this->vals, $val2);
			}
			if (! empty ($val3)) {
				array_push ($this->vals, $val3);
			}
		}
		$this->counter = count ($this->vals);
		$this->current =& $this->vals[0];
	}

	/**
	 * Advances the internal counter and returns the next value.
	 * 
	 * @access	public
	 * @return	mixed
	 * 
	 */
	function next () {
		$this->counter++;
		if ($this->counter >= count ($this->vals)) {
			$this->counter = 0;
		}
		$this->current =& $this->vals[$this->counter];
		return $this->current;
	}

	/**
	 * Returns the internal counter to 0 and returns nothing.
	 * 
	 * @access	public
	 * 
	 */
	function reset () {
		$this->counter = count ($this->vals);
	}
}



?>