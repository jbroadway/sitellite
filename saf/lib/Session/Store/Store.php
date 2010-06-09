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
// This is the base store driver package for saf.Session.  Custom
// drivers are created by sub-classing this package.
//


/**
	 * This is the base store driver package for saf.Session.  Custom
	 * drivers are created by sub-classing this package.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $s = new SessionStore;
	 * 
	 * $s->setProperties (array (
	 * 	'foo'	=> 'bar',
	 * ));
	 * 
	 * if ($s->start ($id)) {
	 * 	// connected
	 * 
	 * 	// set a few session variables
	 * 	$s->set ('name', 'Joe');
	 * 	$s->set ('age', '24');
	 * 
	 * 	// get the value of 'name'
	 * 	$name =$s->get ('name');
	 * 
	 * 	// rename 'name'
	 * 	$oldname = $s->set ('name', 'Jack');
	 * 
	 * 	// unset 'age'
	 * 	$s->set ('age', false);
	 * 
	 * 	// remember these for next time
	 * 	$s->save ();
	 * } else {
	 * 	// not connected
	 * 	echo $s->error;
	 * }
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	Session
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2002-11-09, $Id: Store.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class SessionStore {
	

	/**
	 * A reference to the main session object, in case it's
	 * needed by a specific handler.
	 * 
	 * @access	public
	 * 
	 */
	var $sessObj;

	/**
	 * The error message if an error occurs.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * The store connection resource, if applicable to the individual
	 * driver.
	 * 
	 * @access	public
	 * 
	 */
	var $connection = false;

	/**
	 * The volatile (as in not stored yet) list of values in the
	 * data store.
	 * 
	 * @access	private
	 * 
	 */
	var $_values = array ();

	

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * 
	 */
	function SessionStore () {
	}

	/**
	 * Sets the properties of this object.
	 * 
	 * @access	public
	 * @param	associative array	$properties
	 * 
	 */
	function setProperties ($properties) {
		foreach ($properties as $key => $value) {
			$this->{$key} = $value;
		}
	}

	/**
	 * Starts the session store, initializing any necessary connections,
	 * and retrieving any session values found from a previous web page request.
	 * $id is the session identifier of the current visitor.
	 * 
	 * @access	public
	 * @param	string	$id
	 * @return	boolean
	 * 
	 */
	function start ($id) {
	}

	/**
	 * Retrieves a value from the session store.  Returns false if
	 * the value does not exist.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @return	mixed
	 * 
	 */
	function get ($name) {
		if (isset ($this->_values[$name])) {
			return $this->_values[$name];
		} else {
			return false;
		}
	}

	/**
	 * Sets a value in the session store.  If the value is false,
	 * it will unset it in the store.  If the value is being unset or
	 * set to a new value, then the old value is returned.  If it is a
	 * new value, then the value itself will be returned.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	mixed
	 * 
	 */
	function set ($name, $value = false) {
		if (isset ($this->_values[$name])) {
			$return = $this->_values[$name];
			if ($value === false) {
				unset ($this->_values[$name]);
			} else {
				$this->_values[$name] = $value;
			}
		} else {
			$return = $value;
			$this->_values[$name] = $value;
		}
		return $return;
	}

	/**
	 * Tells the session store to save the values within it.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function save () {
	}

	/**
	 * Closes the session with the store, erasing the values
	 * for this session.
	 * 
	 * @access	public
	 * 
	 */
	function close () {
	}
	
}



?>