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
// HtmlLayout is an editable grid layout system using HTML tables, allowing
// for more complex GUIs to be developed more easily or at least in a more
// automated fashion.
//

$GLOBALS['loader']->import ('saf.GUI.Cell');

/**
	 * HtmlLayout is an editable grid layout system using HTML tables, allowing
	 * for more complex GUIs to be developed more easily or at least in a more
	 * automated fashion.
	 * 
	 * Note: Always reads from top-down *then* left to right.  Take for example the
	 * following 3 x 3 grid:
	 * 
	 *  <pre>
	 *  a1 | b1 | c1
	 * ----+----+----
	 *  a2 | b2 | c2
	 * ----+----+----
	 *  a3 | b3 | c3
	 * </pre>
	 *  
	 *  This grid can also be expressed as:
	 * 
	 *  <pre>
	 *  0,0 | 1,0 | 2,0
	 * -----+-----+-----
	 *  0,1 | 1,1 | 2,1
	 * -----+-----+-----
	 *  0,2 | 1,2 | 2,2
	 *  </pre>
	 * 
	 * This would read a1, a2, a3, b1, b2, b3, etc.  Be mindful of this when looping.
	 * Also of note when referring to cells is that you can refer to them in two
	 * different ways.  The 'a1' notation is acceptable, and you can also pass an array
	 * of two values containing the column (x) and row (y) position of the cell.  Please
	 * note that the first notation starts at 'a' and '1', but the second way starts at
	 * (0, 0).  You can use the translate() method to convert between the two.
	 * 
	 * Also note that there is a cell limit of 701 cols (from a to zz), regardless of
	 * which way you refer to them.  This is probably way more than you should ever
	 * need anyway.  However, should you need more columns, you can use the sub()
	 * method to create sub-layouts inside any cell for practically unlimited depth.
	 * There is no limit on the number of rows available.  
	 * 
	 * New in 1.2:
	 * - Fixed a bug in the render() output that was causing some cells not to appear.
	 * - Increased the column limit from 26 to 701.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $layout = new HtmlLayout (3, 3);
	 * 
	 * // assign a template to cell a1
	 * $layout->assign ('a1', 'test test test');
	 * 
	 * // assign a template to c3
	 * $layout->assign ('c3', 'foo bar');
	 * 
	 * // create a sub-layout in b2
	 * $b2 =& $layout->sub ('b2');
	 * 
	 * // expand b2 one down
	 * $layout->spanRows ('b2', 2);
	 * 
	 * // expand a1 two across
	 * $layout->spanCols ('a1', 3);
	 * 
	 * // set some table properties
	 * $layout->set ('table', 'border', '1');
	 * $layout->set ('table', 'width', '50%');
	 * $layout->set ('table', 'height', '50%');
	 * $layout->set ('table', 'cellspacing', '2');
	 * $layout->set ('table', 'cellpadding', '2');
	 * 
	 * // assign b2 (the sub-layout)'s a1 cell a template
	 * $b2->assign ('a1', 'qwerty');
	 * 
	 * // render
	 * echo $layout->render ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	GUI
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2002-08-18, $Id: Layout.php,v 1.4 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class HtmlLayout {
	/**
	 * Contains the grid.
	 * 
	 * @access	public
	 * 
	 */
	var $matrix = array ();

	/**
	 * Contains all HTML <table> properties for the current object.
	 * 
	 * @access	public
	 * 
	 */
	var $table = array ();

	/**
	 * Contains all HTML <tr> properties for the current object.
	 * 
	 * @access	public
	 * 
	 */
	var $row = array ();

	/**
	 * Contains all HTML <td> properties for the current object,
	 * if this layout is not the top-level layout object.
	 * 
	 * @access	public
	 * 
	 */
	var $cell = array ();

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	integer	$x
	 * @param	integer	$y
	 * 
	 */
	function HtmlLayout ($x = 1, $y = 1) {
		for ($i = 0; $i < $x; $i++) {
			$this->matrix[$i] = array ();
			for ($j = 0; $j < $y; $j++) {
				$this->matrix[$i][$j] = new HtmlCell ();
				$trans = $this->translate ($i, $j);
				$this->{$trans} =& $this->matrix[$i][$j];
			}
		}
		$this->table = array (
			'border' => '0',
			'cellpadding' => '0',
			'cellspacing' => '0',
		);
		$this->row = array (
			'border' => '0',
		);
		$this->cell = array (
			'border' => '0',
			'valign' => 'top',
			'colspan' => '1',
			'rowspan' => '1',
		);
	}

	/**
	 * Assigns a template to the specified child HtmlCell object.
	 * $part may be either a string of the format 'a1', or an array with
	 * two numeric values denoting the cell's x and y position.
	 * 
	 * @access	public
	 * @param	mixed	$part
	 * @param	string	$template
	 * 
	 */
	function assign ($part, $template) {
		if (is_array ($part)) {
			$part = $this->translate ($part);
		}
		if (strtolower (get_class ($this->{$part})) == 'htmlcell') {
			$this->{$part}->template = $template;
		}
	}

	/**
	 * Appends more to the template of the specified child HtmlCell
	 * object. $part may be either a string of the format 'a1', or an
	 * array with two numeric values denoting the cell's x and y position.
	 * 
	 * @access	public
	 * @param	mixed	$part
	 * @param	string	$template
	 * 
	 */
	function append ($part, $template) {
		if (is_array ($part)) {
			$part = $this->translate ($part);
		}
		if (strtolower (get_class ($this->{$part})) == 'htmlcell') {
			$this->{$part}->template .= $template;
		}
	}

	/**
	 * Increases the number of columns by 1.
	 * 
	 * @access	public
	 * 
	 */
	function addCol () {
		$this->matrix[count ($this->matrix)] = array ();
		for ($i = 0; $i < count ($this->matrix[count ($this->matrix) - 2]); $i++) {
			$this->matrix[count ($this->matrix) - 1][$i] = new HtmlCell ();
			$trans = $this->translate (count ($this->matrix) - 1, $i);
			$this->{$trans} =& $this->matrix[count ($this->matrix) - 1][$i];
		}
	}

	/**
	 * Increases the number of rows by 1.
	 * 
	 * @access	public
	 * 
	 */
	function addRow () {
		for ($i = 0; $i < count ($this->matrix); $i++) {
			$this->matrix[$i][] = new HtmlCell ();
			$trans = $this->translate ($i, count ($this->matrix[$i]) - 1);
			$this->{$trans} =& $this->matrix[$i][count ($this->matrix[$i]) - 1];
		}
	}

	/**
	 * Increases the number of columns by 1.  Note that $part may be either
	 * 'table', 'row', or 'cell' to refer to internal properties, or 'a1, a2, etc'
	 * or an array of x and y to refer to pass the property/value pair on to the
	 * set() method of individual cells.
	 * 
	 * @access	public
	 * @param	mixed	$part
	 * @param	string	$property
	 * @param	string	$value
	 * 
	 */
	function set ($part, $property, $value) {
		if ($part == 'table') {
			$this->{$part}[$property] = $value;
		} elseif ($part == 'row') {
			$this->{$part}[$property] = $value;
		} elseif ($part == 'cell') {
			$this->{$part}[$property] = $value;
		} elseif (strtolower (get_class ($this->{$part})) == 'htmlcell') {
			$this->{$part}->set ($property, $value);
		} elseif (is_array ($part)) {
			$part = $this->translate ($part);
			if (strtolower (get_class ($this->{$part})) == 'htmlcell') {
				$this->{$part}->set ($property, $value);
			}
		}
	}

	/**
	 * Returns the specified column as an array of cells.
	 * 
	 * @access	public
	 * @param	integer	$num
	 * @return	array
	 * 
	 */
	function col ($num = 0) {
		return $this->matrix[$num];
	}

	/**
	 * Returns the specified row as an array of cells.
	 * 
	 * @access	public
	 * @param	integer	$num
	 * @return	array
	 * 
	 */
	function row ($num = 0) {
		$r = array ();
		foreach ($this->matrix as $m) {
			array_push ($r, $m[$num]);
		}
		return $r;
	}

	/**
	 * Returns the entire grid as an array of cells, going
	 * top-to-bottom, then left-to-right.
	 * 
	 * @access	public
	 * @return	array
	 * 
	 */
	function walk () {
		$r = array ();
		for ($i = 0; $i < count ($this->matrix); $i++) {
			for ($j = 0; $j < count ($this->matrix[$i]); $j++) {
				array_push ($r, $this->matrix[$i][$j]);
			}
		}
		return $r;
	}

	/**
	 * Create and return a sub HtmlLayout object in the
	 * specified cell.  The number of columns and rows for the
	 * sub-layout can be specified in $xx as an array, or in $xx
	 * and $yy.  $x is the position of the cell to put the sub-
	 * layout in, in either method of notation.
	 * 
	 * @access	public
	 * @param	mixed	$x
	 * @param	mixed	$xx
	 * @param	integer	$yy
	 * @return	reference
	 * 
	 */
	function &sub ($x, $xx = 1, $yy = 1) {
		if (is_array ($x)) {
			$x = $this->translate ($x);
		}
		if (strtolower (get_class ($this->{$x})) == 'htmlcell') {
			if (is_array ($xx)) {
				$yy = $xx[1];
				$xx = $xx[0];
			}
			list ($xp, $yp) = $this->translate ($x);
			$this->matrix[$xp][$yp] = new HtmlLayout ($xx, $yy);
			$this->{$x} =& $this->matrix[$xp][$yp];
		}
		return $this->matrix[$xp][$yp];
	}

	/**
	 * Renders this layout and all of its cells as well.
	 * $td is set to 1 automatically if the layout is a sub-layout
	 * of the top-most layout, otherwise you shouldn't need to change
	 * it at all.
	 * 
	 * @access	public
	 * @param	integer	$td
	 * @return	string
	 * 
	 */
	function render ($td = 0) {
		$r = '';
		if ($td) {
			$r .= "\t\t<td";
			foreach ($this->cell as $key => $value) {
				$r .= ' ' . $key . '="' . str_replace ('"', '&quot;', $value) . '"';
			}
			$r .= ">\n";
		}
		$r .= '<table';
		foreach ($this->table as $key => $value) {
			$r .= ' ' . $key . '="' . str_replace ('"', '&quot;', $value) . '"';
		}
		$r .= ">\n";

		$rows = array ();
		for ($x = 0; $x < count ($this->matrix); $x++) {
			for ($y = 0; $y < count ($this->matrix[$x]); $y++) {
				$rows[$y][$x] = $this->matrix[$x][$y];
			}
		}

		foreach ($rows as $row) {

			$r .= "\t<tr";
			foreach ($this->row as $key => $value) {
				$r .= ' ' . $key . '="' . str_replace ('"', '&quot;', $value) . '"';
			}
			$r .= ">\n";

			foreach ($row as $cell) {
				if (is_object ($cell)) {
					// must wrap HtmlLayout objects in their own <td></td>
//					echo get_class ($cell) . ' ' . count ($this->matrix) . ' ' . count ($row) . '<br />';
					$r .= $cell->render (1, $this->cell);
				}
			}

			$r .= "\t</tr>\n";

		}

		$r .= "</table>\n";
		if ($td) {
			$r .= "\t\t</td>\n";
		}
		return $r;
	}

	/**
	 * Sets the rowspan on the specified cell, and set the cells that
	 * would be in the way to false.  $num is the rowspan.
	 * 
	 * @access	public
	 * @param	mixed	$part
	 * @param	integer	$num
	 * 
	 */
	function spanRows ($part, $num = 1) {
		if (is_array ($part)) {
			$part = $this->translate ($part);
		}
		if (strtolower (get_class ($this->{$part})) == 'htmlcell') {
			$this->{$part}->attrs['rowspan'] = $num;
		} elseif (strtolower (get_class ($this->{$part})) == 'htmllayout') {
			$this->{$part}->cell['rowspan'] = $num;
		}
		$num;
		list ($x, $y) = $this->translate ($part);
		for ($i = 1; $i < $num; $i++) {
			// x stays the same
			$this->matrix[$x][$y + $i] = NULL;
		}
	}

	/**
	 * Sets the colspan on the specified cell, and set the cells that
	 * would be in the way to false.  $num is the colspan.
	 * 
	 * @access	public
	 * @param	mixed	$part
	 * @param	integer	$num
	 * 
	 */
	function spanCols ($part, $num = 1) {
		if (is_array ($part)) {
			$part = $this->translate ($part);
		}
		if (strtolower (get_class ($this->{$part})) == 'htmlcell') {
			$this->{$part}->attrs['colspan'] = $num;
		} elseif (strtolower (get_class ($this->{$part})) == 'htmllayout') {
			$this->{$part}->cell['colspan'] = $num;
		}
		$num;
		list ($x, $y) = $this->translate ($part);
		for ($i = 1; $i < $num; $i++) {
			// y stays the same
			$this->matrix[$x + $i][$y] = NULL;
		}
	}

	/**
	 * Translates between the 'a1, a2, b1, b2' notation and the
	 * array (0, 0), array (0, 1), array (1, 0), array (1, 1) notation.
	 * 
	 * @access	public
	 * @param	mixed	$x
	 * @param	integer	$y
	 * @return	mixed
	 * 
	 */
	function translate ($x, $y = '') {
		if (is_array ($x)) {
			$y = $x[1];
			$x = $x[0];
		}
		if ($y === '') {
			if (preg_match ('/([a-z]+)([0-9]+)/', $x, $regs)) {
				$x = $regs[1];
				$y = $regs[2];
			} else {
				$this->error = 'Notation is invalid!';
				return false;
			}
			$y--;
			if (strlen ($x) == 1) {
				$x = strpos ('abcdefghijklmnopqrstuvwxyz', $x);
			} elseif (strlen ($x) == 2) {
				$one = strpos ('abcdefghijklmnopqrstuvwxyz', $x[0]);
				$two = strpos ('abcdefghijklmnopqrstuvwxyz', $x[1]);
				$x = (($one + 1) * 26) + ($two + 1) - 1;
			} else {
				$this->error = 'X value is too long!';
				return false;
			}
			return array ($x, $y);
		} else {
			$y++;
			if ($x <= 25) {
				$x = substr ('abcdefghijklmnopqrstuvwxyz', $x, 1);
			} else {
				$x++;
				$one = floor ($x / 26);
				$one--;
				$two = $x % 26;
				$two--;
				if ($two < 0) {
					$one--;
				}
				$x = substr ('abcdefghijklmnopqrstuvwxyz', $one, 1);
				$x .= substr ('abcdefghijklmnopqrstuvwxyz', $two, 1);
			}
			return $x . $y;
		}
	}
}



?>