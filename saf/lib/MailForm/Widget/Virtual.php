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
// Pseudo-Widget that displays information from a database query
// or user-defined function.
//

/**
	 * Pseudo-Widget that displays information from a database query
	 * or user-defined function.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_virtual ('virtual_test');
	 * 
	 * $widget->setValue (
	 * 	'query',
	 * 	'select round(((clicks / views) * 100), 2) as clicks from banner where id = ??'
	 * );
	 * $widget->bindValues = array ('pkey value from banner table');
	 * 
	 * $obj = $widget->getValue ();
	 * echo $obj->clicks;
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2002-08-31, $Id: Virtual.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_virtual extends MF_Widget {
	/**
	 * A function to call to get the value of this widget.  This
	 * function must act on a $cgi object.
	 * 
	 * @access	public
	 * 
	 */
	var $function;

	/**
	 * A database query to execute to get the value of this widget.
	 * 
	 * @access	public
	 * 
	 */
	var $query;

	/**
	 * A list of bind values to pass to the database query.
	 * 
	 * @access	public
	 * 
	 */
	var $bindValues = array ();

	/**
	 * A column that will be returned from the database query.  If this
	 * column is specified, then instead of returning an object or array, the
	 * getValue() method will return the value of this column from the first
	 * result returned from the database.
	 * 
	 * @access	public
	 * 
	 */
	var $column;

	/**
	 * If the database query fails, this will contain the error message.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'virtual';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_virtual ($name) {
		$this->name = $name;
		$this->passover_isset = true;
		$this->error_message = '';
	}

	/**
	 * Validates the widget against its set of $rules.  Returns false
	 * on failure to pass any rule.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	object	$form
	 * @param	object	$cgi
	 * @return	boolean
	 * 
	 */
	function validate ($value, $form, $cgi) {
		return true;
	}

	/**
	 * Sets either the $function or $query property.  If the property
	 * name is specified as $key, then $value is what it is set to, otherwise
	 * it checks to see if the $key is an existing function and if so sets
	 * $function to $key, otherwise sets $query to $key.
	 * 
	 * @access	public
	 * @param	string	$key
	 * @param	string	$value
	 * 
	 */
	function setValue ($key = '', $value = '') {
		if (! empty ($value)) {
			$this->{$key} = $value;
		} elseif (! empty ($key)) {
			if (function_exists ($key)) {
				$this->function = $key;
			} else {
				$this->query = $key;
			}
		}
	}

	/**
	 * Fetches the actual value for this widget.  Returns whatever
	 * the function or database query returns.  If $column is specified,
	 * returns the value of the $column field from the first record of the
	 * database results.
	 * 
	 * @access	public
	 * @param	object	$cgi
	 * @return	mixed
	 * 
	 */
	function getValue ($cgi = '') {
		if (! empty ($this->function)) {
			$func = $this->function;
			return $func ($cgi);
		} elseif (! empty ($this->query)) {
			global $db;
			$res = $db->fetch ($this->query, $this->bindValues);
			if (! $res) {
				$this->error = $db->error;
				return '';
			} elseif (is_object ($res)) {
				if (isset ($res->{$this->column})) {
					return $res->{$this->column};
				}
			} elseif (is_array ($res)) {
				if (isset ($res[0]->{$this->column})) {
					return $res[0]->{$this->column};
				}
			}
			return $res;
		}
		return '';
	}

	/**
	 * Returns the display HTML for this widget.  The optional
	 * parameter determines whether or not to automatically display the widget
	 * nicely, or whether to simply return the widget (for use in a template).
	 * 
	 * @access	public
	 * @param	boolean	$generate_html
	 * @return	string
	 * 
	 */
	function display ($generate_html = 0) {
		return '';
	}
}



?>