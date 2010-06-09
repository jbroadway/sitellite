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
// XMLDoc creates XML documents for you using DOM-like method calls.
// XMLDoc is lightweight and fast, but does not deal with things like
// namespaces or encoding.
//

/**
	 * XMLAttr stores all the XML attributes of your document.
	 * 
	 * New in 1.2:
	 * - Added a write() method.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $attr = new XMLAttr ('name', 'value');
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	XML
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2003-01-19, $Id: Attr.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class XMLAttr {
	/**
	 * Name of the attribute.
	 * 
	 * @access	public
	 * 
	 */
	var $name;

	/**
	 * Value of the attribute.
	 * 
	 * @access	public
	 * 
	 */
	var $value;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$value
	 * 
	 */
	function XMLAttr ($name, $value) {
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * Writes the attribute in XML format.  $space determines
	 * whether you want a preceeding space automatically inserted.
	 * 
	 * @access	public
	 * @param	boolean	$space
	 * @return	string
	 * 
	 */
	function write ($space = false) {
		if ($space) {
			$space = ' ';
		} else {
			$space = '';
		}
		return $space . $this->name . '="' . $this->value . '"';
	}
}



?>