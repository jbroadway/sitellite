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
// Buffer implements string, array, and tree structure buffering for
// classes extending saf.Parser.
//


/**
	 * Buffer implements string, array, and tree structure buffering for
	 * classes extending saf.Parser.  This makes management of cumulative data
	 * easier during the analysis of a big ugly pile of tokens.
	 * 
	 * One might call the combination of this and saf.Parser something of a
	 * Finite State Machine (FSM), but not quite.  Callbacks (like transitions)
	 * are handled by saf.Parser, which would use Buffer to store data in a
	 * Push-Down Automata (PDA) like manner.  The current state can be stored
	 * in a private property of the saf.Parser object.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $buff = new Buffer;
	 * 
	 * // sets $output to 'Foo bar'
	 * $buff->set ('Foo bar');
	 * 
	 * // adds ' asdf qwerty to $output
	 * $buff->append (' asdf qwerty');
	 * 
	 * // returns $output
	 * echo $buff->get ();
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	Parser
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	0.8, 2003-01-20, $Id: Buffer.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class Buffer {
	

	/**
	 * The string buffer.
	 * 
	 * @access	public
	 * 
	 */
	var $output = '';

	/**
	 * The array buffer.
	 * 
	 * @access	public
	 * 
	 */
	var $buffers = array ();

	

	/**
	 * Sets a buffer value.  If only one parameter is passed,
	 * it will assume that you want it to go into the $output property.
	 * If two are passed, it will put it into $buffers.  If the first
	 * value is false, it will append it to $buffers without specifying
	 * the key.  Returns true for named buffers and data sent to $output,
	 * and returns the number of the buffered data in the absense of a
	 * key.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	mixed	$data
	 * @return	mixed
	 * 
	 */
	function set ($name, $data = false) {
		if ($data === false) {
			$this->output = $name;
			return true;
		} else {
			if ($name === false) {
				$this->buffers[] = $data;
				return count ($this->buffers) - 1;
			} else {
				$this->buffers[$name] = $data;
				return true;
			}
		}
	}

	/**
	 * Appends to a buffer value.  Same rules as set() apply
	 * as to how the parameters are interpreted (this applies to all
	 * methods here), and what is returned.  Note: On unnamed arrays,
	 * append() appends the value to the end of the array, and not
	 * to the end of the last element in the array.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	mixed	$data
	 * @return	mixed
	 * 
	 */
	function append ($name, $data = false) {
		if ($data === false) {
			$this->output .= $name;
			return true;
		} else {
			if ($name === false) {
				$this->buffers[] = $data;
				return count ($this->buffers) - 1;
			} else {
				$this->buffers[$name] .= $data;
				return true;
			}
		}
	}

	/**
	 * Preppends to a buffer value.  Note: On unnamed arrays,
	 * append() appends the value to the end of the array, and not
	 * to the start of an element in the array.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	mixed	$data
	 * @return	mixed
	 * 
	 */
	function prepend ($name, $data = false) {
		if ($data === false) {
			$this->output = $name . $this->output;
			return true;
		} else {
			if ($name === false) {
				$this->buffers[] = $data;
				return count ($this->buffers) - 1;
			} else {
				$this->buffers[$name] = $data . $this->buffers[$name];
				return true;
			}
		}
	}

	/**
	 * Clears a buffer value.
	 * 
	 * @access	public
	 * @param	mixed	$name
	 * 
	 */
	function clear ($name = false) {
		if ($name === false) {
			$this->output = '';
		} else {
			unset ($this->buffers[$name]);
		}
	}

	/**
	 * Retrieves the specified value from the buffers.
	 * 
	 * @access	public
	 * @param	mixed	$name
	 * @return	mixed
	 * 
	 */
	function get ($name = false) {
		if ($name === false) {
			return $this->output;
		} else {
			return $this->buffers[$name];
		}
	}

	/**
	 * Returns the entire $buffers array.
	 * 
	 * @access	public
	 * @return	array
	 * 
	 */
	function getAll () {
		return $this->buffers;
	}

	
}



?>