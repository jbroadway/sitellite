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

$GLOBALS['loader']->import ('saf.XML.Doc.Node');

/**
	 * XMLDoc creates XML documents for you using DOM-like method calls.
	 * XMLDoc is lightweight and fast, but does not deal with things like
	 * namespaces or encoding.
	 * 
	 * New in 1.2:
	 * - Added method query (), which allows users to traverse a set of nodes more
	 *   easily and more legibly by using the most basic subset of XPath.  Currently
	 *   supports only the most basic syntax (/node1/node2/node3).
	 * - Added the $error property.
	 * 
	 * New in 1.4:
	 * - Added a $level parameter to the write() method, which is passed on to the
	 *   root node.  -1 signifies no auto-indenting.
	 * 
	 * New in 1.6:
	 * - Added two new methods: makeDoc() and writeToFile().  Also added a $filename
	 *   property.
	 * 
	 * New in 1.8:
	 * - Added a $doctype property.
	 * 
	 * New in 2.0:
	 * - Added a new parameter to the query() method that lets you return an array
	 *   of references to the resulting nodes instead of copies.  This makes it
	 *   easier to use the query() method in conjunction with document updates.
	 * 
	 * New in 2.2:
	 * - Added a makeMenu() method which uses saf.GUI.Menu to make it easier to
	 *   display a document as a hierarchy using templates.
	 * - Updated query() to use the new saf.XML.Doc.Query class, which implements
	 *   a simple query language based on XPath.  See saf.XML.Doc.Query for specifics
	 *   and examples.
	 * - Added makeObj() and makeRef() object methods.
	 * 
	 * New in 2.4:
	 * - Added a cache() method, which works with SloppyDOM's parseFromFile() method
	 *   to make caching of XML documents super easy.
	 * 
	 * New in 2.6:
	 * - Added a propagateCallback() method, which compliments the new callback
	 *   functionality in saf.XML.Doc.Node.  For more info, see that package in
	 *   saf/docs or DocReader.
	 * 
	 * New in 2.8:
	 * - Added an $xquery property, and modified query() to work with the new
	 *   saf.XML.Doc.Query package.
	 * 
	 * New in 3.0:
	 * - Added a named alias for the $root node, so that it may be referred to
	 *   by name for convenience.  For example: $doc->_html.  Note the
	 *   underscore, used to prevent naming conflicts.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $doc = new XMLDoc ();
	 * 
	 * // create a basic xhtml document
	 * $root =& $doc->addRoot ('html');
	 * 
	 * $head =& $root->addChild ('head');
	 * $title =& $head->addChild ('title', 'Lux\'s Home Page');
	 * $link =& $head->addChild ('link');
	 * $link->setAttribute ('rel', 'stylesheet');
	 * $link->setAttribute ('type', 'text/css');
	 * $link->setAttribute ('href', 'http://127.0.0.4/css/site.css');
	 * 
	 * $body =& $root->addChild ('body');
	 * $anchor_top =& $body->addChild ('a');
	 * $anchor_top->setAttribute ('name', 'top');
	 * 
	 * $h1 =& $body->addChild ('h1', 'Lux\'s Home Page');
	 * $img =& $body->addChild ('img');
	 * $img->setAttribute ('src', '/pix/meeting.jpg');
	 * $img->setAttribute ('alt', 'Welcome Image');
	 * $img->setAttribute ('border', '0');
	 * $img->setAttribute ('style', 'float: left');
	 * 
	 * $p1 =& $body->addChild ('p', 'Lux is a guy from Windsor, Ontario.  He moved to Winnipeg in 1999.');
	 * $p2 =& $body->addChild ('p', 'This page is just Lux\'s place to put up pictures of his cats, and other things you probably don\'t care to see.');
	 * $copyright =& $body->addChild ('p', 'Copyright (c) 2001, Lux');
	 * $copyright->setAttribute ('align', 'center');
	 * 
	 * echo $doc->write ();
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	XML
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.8, 2003-07-11, $Id: Doc.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class XMLDoc {
	/**
	 * XML version.  Default is 1.0
	 * 
	 * @access	public
	 * 
	 */
	var $version;

	/**
	 * Encoding of the XML document.  Default is 'utf-8'.
	 * Please note: Actual encoding of data is not handled by these
	 * classes.
	 * 
	 * @access	public
	 * 
	 */
	var $encoding;

	/**
	 * May contain the entire DOCTYPE declaration tag, including the
	 * < and >.
	 * 
	 * @access	public
	 * 
	 */
	var $doctype;

	/**
	 * The root node of the XML document.
	 * 
	 * @access	public
	 * 
	 */
	var $root;

	/**
	 * Any internal error message involved in using this class.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * If this object was read in from a file, it may be specified here.
	 * 
	 * @access	public
	 * 
	 */
	var $filename;

	/**
	 * If query() has been called, this will contain the XMLDocQuery object.
	 * 
	 * @access	public
	 * 
	 */
	var $xquery;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$version
	 * @param	string	$encoding
	 * 
	 */
	function XMLDoc ($version = '1.0', $encoding = 'utf-8') {
		$this->version = $version;
		$this->encoding = $encoding;
	}

	/**
	 * Creates the root node object of the document.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @return	resource
	 * 
	 */
	function &addRoot ($name) {
		// returns XMLNode object, stores internal pointer
		$this->root = new XMLNode ($name, '', 0, $this);
		if (! isset ($this->{'_' . $name})) {
			$this->{'_' . $name} =& $this->root;
		}
		return $this->root;
	}

	/**
	 * Generates the XML document you've created.
	 * 
	 * @access	public
	 * @param	integer	$level
	 * @return	string
	 * 
	 */
	function write ($level = 0) {
		// returns XML
		$data = '<?xml';
		if (! empty ($this->version)) {
			$data .= ' version="' . $this->version . '"';
		}
		if (! empty ($this->encoding)) {
			$data .= ' encoding="' . $this->encoding . '"';
		}
		$data .= CLOSE_TAG . "\n";

		if (! empty ($this->doctype)) {
			$data .= $this->doctype . "\n";
		}

		$out = $this->root->write ($level);
		if ($out === false) { // callback failed
			return false;
		}
		return $data . $out;
	}

	/**
	 * Returns a set of nodes, making it easier to traverse elements
	 * in a loop, and making the code more legible as well.  Accepts a very
	 * elementary and minimal language based on XPath (see saf.XML.Doc.Query for
	 * specifics and examples).  Returns an array of references to matching
	 * nodes.  Note: The $ref parameter is deprecated and doesn't do anything.
	 * Results are always returned as references.
	 * 
	 * @access	public
	 * @param	string	$path
	 * @param	boolean	$ref
	 * @return	array
	 * 
	 */
	function query ($path, $ref = 0) {
		if (! is_object ($this->xquery)) {
			global $loader;
			$loader->import ('saf.XML.Doc.Query');
			$this->xquery = new XMLDocQuery ();
		}
		return $this->xquery->query ($this, $path);
	}

	/**
	 * Takes an associative array or an object and returns an XMLDoc object
	 * created from it.  $toptag is the name of the root node.
	 * 
	 * @access	public
	 * @param	mixed	$obj
	 * @param	string	$toptag
	 * @return	object
	 * 
	 */
	function &makeDoc ($obj, $toptag) {
		$doc = new XMLDoc;
		$root =& $doc->addRoot ($toptag);
		if (is_object ($obj)) {
			foreach (get_object_vars ($obj) as $node => $content) {
				$root->addChild ($node, $content);
			}
		} elseif (is_array ($obj)) {
			foreach ($obj as $node => $content) {
				$root->addChild ($node, $content);
			}
		} else {
			$this->error = '$obj is not an object or an array.';
		}
		return $doc;
	}

	/**
	 * Writes the current XMLDoc object to the specified file.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @param	integer	$level
	 * @return	boolean
	 * 
	 */
	function writeToFile ($file = '', $level = 0) {
		if (empty ($file)) {
			$file = $this->filename;
		}
		if (empty ($file)) {
			$this->error = 'No $file parameter or $filename property was specified.';
			return false;
		}
		$fp = @fopen ($file, 'w');
		if (! $fp) {
			$this->error = 'Opening file failed.';
			return false;
		}
		$data = $this->write ($level);
		fwrite ($fp, $data);
		fclose ($fp);
		return true;
	}

	/**
	 * Turns this document into a saf.GUI.Menu object, making it easy
	 * to display a document as a hierarchy using templates.
	 * 
	 * @access	public
	 * @return	object
	 * 
	 */
	function makeMenu () {
		global $loader;
		$loader->import ('saf.GUI.Menu');
		$menu = new Menu ();
		return $this->root->_makeMenu ($menu);
	}

	/**
	 * Calls the makeObj() method on the root node of the document tree.
	 * 
	 * @access	public
	 * @return	object
	 * 
	 */
	function makeObj () {
		return $this->root->makeObj ();
	}

	/**
	 * Calls the makeRefObj() method on the root node of the document tree.
	 * 
	 * @access	public
	 * @return	object reference
	 * 
	 */
	function &makeRefObj () {
		return $this->root->makeRefObj ();
	}

	/**
	 * Caches the current object to a file by serializing itself.  Only updates
	 * the cache file if the file modification time of the original file ($filename
	 * property must be set or you will get an error) has changed.  This method
	 * works with SloppyDOM's parseFromFile() method to make caching XML files
	 * super easy.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @return	boolean
	 * 
	 */
	function cache ($file) {
		if (! file_exists ($file) || filemtime ($this->filename) >= @filemtime ($file)) {
			//echo 'Caching document<br />';
			$fp = fopen ($file, 'w');
			if (! $fp) {
				$this->error = 'Failed to open cache file!';
				return false;
			}
			fwrite ($fp, serialize ($this));
			fclose ($fp);
		} else {
			//echo 'Did not need to cache this time<br />';
		}
		return true;
	}

	/**
	 * Propagates a callback setting to the root node and all of its
	 * child nodes as well.  Useful for setting a "default" callback setting
	 * which can then be overridden on a per-node basis, and for adding callbacks
	 * to documents which were recreated from a pre-existing data source.
	 * 
	 * @access	public
	 * @param	string	$startFunction
	 * @param	string	$endFunction
	 * @param	object reference	$obj
	 * 
	 */
	function propagateCallback ($startFunction, $endFunction = false, &$obj) {
		return $this->root->propagateCallback ($startFunction, $endFunction, $obj);
	}
}



?>