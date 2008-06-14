<?php
//
// +----------------------------------------------------------------------+
// | Sitellite - Content Management System                                |
// +----------------------------------------------------------------------+
// | Copyright (c) 2007 Simian Systems                                    |
// +----------------------------------------------------------------------+
// | This software is released under the GNU General Public License (GPL) |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GPL Software License along    |
// | with this program; if not, write to Simian Systems, 242 Lindsay,     |
// | Winnipeg, MB, R3N 1H1, CANADA.  The License is also available at     |
// | the following web site address:                                      |
// | <http://www.sitellite.org/index/license>                             |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <lux@simian.ca>                                |
// +----------------------------------------------------------------------+
//
// SLiPWriter is an XML to SLiP converter based on the XMLDoc and XMLNode
// callback functionality.
//


/**
	 * SLiPWriter is an XML to SLiP converter based on the XMLDoc and XMLNode
	 * callback functionality.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $loader->import ('saf.Sloppy');
	 * 
	 * // load the converter
	 * $loader->import ('saf.XML.SLiP.Writer');
	 * 
	 * $sloppy = new SloppyDOM ();
	 * 
	 * // create an instance of our converter
	 * $slip = new SLiPWriter ();
	 * 
	 * $doc = $sloppy->parse ('<users>
	 * 	<user type="admin">
	 * 		<name>Lux</name>
	 * 		<email>lux@simian.ca</email>
	 * 	</user>
	 * 	<user type="suit">
	 * 		<name>Josh</name>
	 * 		<email>josh@simian.ca</email>
	 * 	</user>
	 * </users>');
	 * 
	 * // bind the converter to the document object
	 * $doc->propagateCallback ('_start', false, $slip);
	 * 
	 * // output SLiP document (note the ->root-> in there,
	 * // that's so we don't get a little bit of <?xml version="1.0"...
	 * // at the top of our SLiP document)
	 * echo $doc->root->write ();
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	XML
	 * @author	John Luxford <lux@simian.ca>
	 * @copyright	Copyright (C) 2001-2003, Simian Systems Inc.
	 * @license	http://www.sitellite.org/index/license	Simian Open Software License
	 * @version	1.0, 2002-10-17, $Id: Writer.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class SLiPWriter {
	

	/**
	 * Callback method.  A callback method always takes an
	 * XMLNode object as its first parameter, and the level (depth)
	 * into the XML tree we've gotten to as its second.
	 * 
	 * @access	public
	 * @param	object	$node
	 * @param	integer	$level
	 * @return	string
	 * 
	 */
	function _start ($node, $level = 0) {
		$res = '';
		$indent = '';
		for ($i = 0; $i < $level; $i++) {
			$indent .= "\t";
		}
		if (! empty ($node->comment)) {
			$res .= $indent . '# ' . $node->comment . "\n";
		}
		$res .= $indent;
		$res .= $node->name;
		if (count ($node->attributes) > 0) {
			$res .= '(';
			$a = array ();
			foreach ($node->attributes as $attr) {
				$a[] = $attr->name . '="' . str_replace ('"', '\\"', $attr->value) . '"';
			}
			$res .= join (',', $a) . ')';
		}
		if (! empty ($node->content)) {
			$content = wordwrap ($node->content);
			if (preg_match ('/[\r\n]/', $content)) {
				$res .= ": \"\"\"\n";
				$res .= $indent . preg_replace ('/(\r\n|\n\r|\r|\n)/', $indent . "\n", $content) . "\n";
				$res .= $indent . "\"\"\"";
			} else {
				$res .= ': "' . str_replace ('"', '\\"', $content) . '"';
			}
		} else {
			$res .= ':';
		}
		return $res . "\n";
	}
	
}



?>