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
// Assoc widget.  Displays editable key/value pairs, like an associative
// array.
//

/**
	 * Assoc widget.  Displays editable key/value pairs, like an associative
	 * array.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_assoce ('name');
	 * $widget->setValue (array ('foo' => '...'));
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.4, 2002-08-25, $Id: Assoc.php,v 1.1 2005/06/30 23:55:26 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_assoc extends MF_Widget {
	/**
	 * A way to pass extra parameters to the HTML form tag, for
	 * example 'enctype="multipart/formdata"'.
	 * 
	 * @access	public
	 * 
	 */
	var $extra = '';

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'assoc';

	/**
	 * Ignore whether a CGI value of this widget name is set.
	 *
	 * @access private
	 *
	 */
	var $passover_isset = true;

	/**
	 * Number of key/value pairs.
	 *
	 * @access public
	 *
	 */
	var $lines = 1;

	/**
	 * Whether the keys are editable or not.
	 *
	 * @access public
	 *
	 */
	var $keys_editable = false;

	/**
	 * Name of the key column.
	 *
	 * @access public
	 *
	 */
	var $key_name = 'Name';

	/**
	 * Name of the value column.
	 *
	 * @access public
	 *
	 */
	var $value_name = 'Value';

	/**
	 * Internal copy of the values.
	 *
	 * @access private
	 *
	 */
	var $_internal_array = array ();

	/**
	 * The template used to render the widget.
	 *
	 * @access public
	 *
	 */
	var $_template = '
	<tr>
		<td class="label" valign="top"><label for="{name}" id="{name}-label" {invalid}>{filter none}{label}{end filter}</label></td>
		<td class="field">
			<table border="0" cellpadding="3" cellspacing="1" width="100%">
				<tr>
					<td width="50%"><strong>{key_name}</strong></td>
					<td width="50%"><strong>{value_name}</strong></td>
				</tr>
				{loop obj._internal_array}
				<tr>
					<td>
						{if obj.keys_editable}
							<input type="text" name="data_key_{loop/_key}" value="{loop/_key}" />
						{end if}
						{if else}
							<input type="hidden" name="data_key_{loop/_key}" value="{loop/_key}" />
							{loop/_key}
						{end if}
					</td>
					<td><input type="text" name="data_value_{loop/_key}" value="{loop/_value}" /></td>
				</tr>
				{end loop}
			</table>
		</td>
	</tr>
';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_assoc ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);
		$this->passover_isset = true;
	}

	/**
	 * Sets the actual value for this widget.  An optional second
	 * parameter can be passed, which is unused here, but can be used in
	 * complex widget types to assign parts of a value and piece it together
	 * from multiple physical form fields.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	string	$inner_component
	 * 
	 */
	function setValue ($value = '', $inner_component = '') {
		if (! empty ($inner_component)) {
			$this->_internal_array[$inner_component] = $value;
		} elseif (is_array ($value)) {
			$this->_internal_array = $value;
		}
	}

	/**
	 * Fetches the actual value for this widget.
	 * 
	 * @access	public
	 * @param	object	$cgi
	 * @return	string
	 * 
	 */
	function getValue ($cgi = '') {
		if (! is_object ($cgi)) {
			return $this->_internal_array;
		} else {
			foreach (array_keys ($this->_internal_array) as $k) {
				if (isset ($cgi->{'data_value_' . $k})) {
					$this->_internal_array[$k] = $cgi->{'data_value_' . $k};
				}
				if (isset ($cgi->{'data_key_' . $k}) && $k != $cgi->{'data_key_' . $k}) {
					$this->_internal_array[$cgi->{'data_key_' . $k}] = $this->_internal_array[$k];
					unset ($this->_internal_array[$k]);
				}
			}
			return $this->_internal_array;
		}
	}

	/**
	 * Gives this widget a default value.  Accepts a date string
	 * of the format 'YYYY-MM-DD'.
	 * 
	 * @access	public
	 * @param	string	$value
	 * 
	 */
	function setDefault ($value) {
		$this->setValue ($value);
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
		$this->label = template_simple ($this->label_template, $this, '', true);
		return template_simple ($this->_template, $this);
	}
}

?>