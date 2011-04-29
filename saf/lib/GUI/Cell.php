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
// HtmlCell is the individual cell class for the HtmlLayout grid class.
//

$GLOBALS['loader']->import ('saf.GUI.Layout');

/**
	 * HtmlCell is the individual cell class for the HtmlLayout grid class.
	 * 
	 * Note: In addition to requiring the HtmlLayout class to really do anything
	 * useful, HtmlCell also requires a global saf.Template.Simple object called $simple
	 * be defined.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $cell = new HtmlCell ('some_template.spt');
	 * 
	 * $cell->set ('border', '2');
	 * 
	 * echo $cell->render ($fill_object);
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	GUI
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2002-08-17, $Id: Cell.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class HtmlCell {
	/**
	 * Contains the template to use to fill this cell with upon calling render().
	 * 
	 * @access	public
	 * 
	 */
	var $template;
           
	/**
	 * The object or associative array to use to fill the template with upon
	 * calling render().
	 * 
	 * @access	public
	 * 
	 */
	var $fill;
           
	/**
	 * The span of this cell.  Set to -1 and the cell will disappear.  Use
	 * $this->attrs['colspan'] or ['rowspan'] to set these properties in the
	 * corresponding HTML <td> tag.
	 * 
	 * @access	public
	 * 
	 */
	var $span;
           
	/**
	 * Contains all HTML <td> properties for the current object.
	 * 
	 * @access	public
	 * 
	 */
	var $attrs = array ();
           
	/**
	 * $default is an optional default value to place in cells whose
	 * templates result in no output.
	 * 
	 * @access	public
	 * 
	 */
	var $default;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$template
	 * 
	 */
	function HtmlCell ($template = '') {
		$this->template = $template;
		$this->attrs = array ();
		$this->fill = '';
		$this->span = 0;
	}

	/**
	 * Sets the appropriate key/value pair in $attrs.
	 * 
	 * @access	public
	 * @param	string	$property
	 * @param	string	$value
	 * 
	 */
	function set ($property, $value) {
		$this->attrs[$property] = $value;
	}

	/**
	 * Renders this cell into an HTML <td> tag filled with
	 * the output of the specified template.  $object may replace the $fill
	 * property if specified.  $properties is a list of properties of the
	 * <td> tag, and $default is an optional default value to place
	 * in cells whose templates result in no output.
	 * 
	 * @access	public
	 * @param	object	$object
	 * @param	associative array	$properties
	 * @param	string	$default
	 * @return	string
	 * 
	 */
	function render ($object = '', $properties = array (), $default = '&nbsp;') {
		if ($this->span >= 0) { // don't show if span is less than 0
			$r = "\t\t<td";
			foreach ($this->attrs as $key => $value) {
				$properties[$key] = $value;
			}
			foreach ($properties as $key => $value) {
				$r .= ' ' . $key . '="' . str_replace ('"', '&quot;', $value) . '"';
			}
			$r .= '>';

			if (! empty ($object)) {
				$this->fill = $object;
			}

			// render template
			if (! empty ($this->template)) {
				global $simple;
				$r .= $simple->fill ($this->template, $this->fill);
			} else {
				if (empty ($default)) {
					$default = $this->default;
				}
				$r .= $default;
			}

			$r .= "</td>\n";
			return $r;
		}
	}
}



?>