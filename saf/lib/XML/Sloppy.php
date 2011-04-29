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
// SloppyDOM is a lightweight XML parser that creates an in-memory copy
// of the XML document using the XMLDoc class.
//

$GLOBALS['loader']->import ('saf.XML.Doc');

/**
	 * SloppyDOM is a lightweight XML parser that creates an in-memory copy
	 * of the XML document using the XMLDoc class.  This allows lightweight document
	 * parsing and modifications to take place.  Note: does not maintain DOCTYPE
	 * declarations and comments (hence Sloppy).
	 * 
	 * New in 1.2:
	 * - Added support for CDATA blocks.
	 * 
	 * New in 1.4:
	 * - Fixed a small bug that caused parsing to hang on multi-line comments.
	 * 
	 * New in 2.0:
	 * - Rewrote the class to use the PHP expat extension (like it should have from
	 *   1.0), so it's much faster and more stable now.  It's also backward-compatible
	 *   with 1.x.
	 * - Also added the parseFromFile() method, and the $encoding property, which
	 *   specifies the type of encoding to send to the internal expat parser.
	 *   ISO-8859-1 is the default, and US-ASCII and UTF-8 are also supported.
	 * - Added the following properties, which hold information pertaining to parsing
	 *   errors: $error, $err_code, $err_line, $err_byte, and $err_column.
	 * 
	 * New in 2.2:
	 * - Fixed a spacing issue with parsing then resaving XML files, where extra
	 *   space was being added each time a file is edited.
	 * 
	 * New in 2.4:
	 * - Changed parseFromFile() to set the XMLDoc's $filename property before returning
	 *   the object.  Also improved error handling slightly in that method.
	 * 
	 * New in 2.6:
	 * - Added an optional $cacheFile parameter to parseFromFile(), which works with
	 *   XMLDoc's cache() method to make caching an XML document effortless.
	 * 
	 * New in 2.8:
	 * - Fixed some things that were causing allow_call_time_pass_by_reference (or
	 *   something like that) warnings.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $sd = new SloppyDOM ();
	 * 
	 * if ($new_doc = $sd->parse ($xml_data)) {
	 * 	// use $new_doc
	 * } else {
	 * 	echo $sd->error;
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	XML
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.8, 2002-10-10, $Id: Sloppy.php,v 1.5 2008/02/17 13:31:13 lux Exp $
	 * @access	public
	 * 
	 */

class SloppyDOM {
	/**
	 * Will contain the error message in the event of a parsing error, or false
	 * otherwise, so that it can be used in an if (error) statement.
	 * 
	 * @access	public
	 * 
	 */
	var $error; // the error message if an error occurs

	/**
	 * Will contain the error code in the event of a parsing error.
	 * 
	 * @access	public
	 * 
	 */
	var $err_code; // the error code if an error occurs

	/**
	 * Will contain the error line in the event of a parsing error.
	 * 
	 * @access	public
	 * 
	 */
	var $err_line; // the current line if an error occurs

	/**
	 * Will contain the error byte index in the event of a parsing error.
	 * 
	 * @access	public
	 * 
	 */
	var $err_byte; // the current byte index if an error occurs

	/**
	 * Will contain the error column number in the event of a parsing error.
	 * 
	 * @access	public
	 * 
	 */
	var $err_colnum; // the current column number if an error occurs

	/**
	 * The XMLDoc object that was last parsed.
	 * 
	 * @access	public
	 * 
	 */
	var $doc; // the XMLDoc document

	/**
	 * The parser created during calls to parse().  Destroyed before returning
	 * from parse().
	 * 
	 * @access	private
	 * 
	 */
	var $parser; // the expat xml parser handler

	/**
	 * A list of references to the next node in the XMLDoc hierarchy.
	 * Destroyed before returning from parse().
	 * 
	 * @access	private
	 * 
	 */
	var $parent = array (); // on tag_open tag is pushed, on tag_close tag is popped

	/**
	 * The optional encoding type to use when creating the XML parser resource.
	 * 
	 * @access	public
	 * 
	 */
	var $encoding;

	/**
	 * The contents of the current comment tag.
	 * 
	 * @access	public
	 * 
	 */
	var $comment;

	var $comment_open = false;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$encoding
	 * 
	 */
	function SloppyDOM ($encoding = 'ISO-8859-1') {
		$this->error = false;
		$this->encoding = $encoding;
		$this->comment = '';
	}

	/**
	 * Parses a string of XML data and returns an XMLDoc object representation of it.
	 * Note: Returns false on failure.  If you include the name of a $cacheFile,
	 * parseFromFile() will compare its file modification time to the mod time
	 * of the original file and use the cached copy if the original has not
	 * been modified.  This means that a document can be cached using XMLDoc's
	 * cache() method, and then only have to be parsed once after any change
	 * is made.  Since parsing XML can be a memory intensive process, especially
	 * on larger documents, having the luxury of not parsing the same document
	 * upon each visitor request makes it much more appealing to use XML in web
	 * applications.  Even XML configuration files are not unreasonable (although
	 * you want to be careful not to cache a configuration file within the
	 * document root of your site!!!).  Cache files are simply serialized XMLDoc
	 * objects.
	 * 
	 * @access	public
	 * @param	string	$filename
	 * @param	string	$cacheFile
	 * @return	object
	 * 
	 */
	function parseFromFile ($filename, $cacheFile = '') {
		if (! empty ($cacheFile)) {
			//echo 'File is cacheable<br />';
			if (filemtime ($filename) < @filemtime ($cacheFile)) {
				//echo 'Using cached copy<br />';
				// use cached copy
				$data = implode ('', file ($cacheFile));
				$doc = unserialize ($data);
				if (is_object ($doc)) {
					return $doc;
				}
				// else, proceed to reload below
			}
			// otherwise, proceed to reload below
		}
		//echo 'Using original copy<br />';
		if (@is_file ($filename)) {
			$data = implode ('', file ($filename));
		} else {
			$this->error = 'SloppyDOM Error: Reading XML from file failed!';
			return false;
		}
		$doc = $this->parse ($data);
		if (! $doc) {
			return false;
		} else {
			$doc->filename = $filename;
		}
		return $doc;
	}

	/**
	 * Parses a string of XML data and returns an XMLDoc object representation of it.
	 * Note: Returns false on failure.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @return	object
	 * 
	 */
	function parse ($data) {
		// create the xml parser now, and declare the handler methods
		$this->parser = xml_parser_create ($this->encoding);
		if (! $this->parser) {
			$this->error = 'SloppyDOM Error: Failed to create an XML parser!';
			return false;
		}
		xml_set_object ($this->parser, $this);
		if (! xml_set_element_handler ($this->parser, array (&$this, 'tag_open'), array (&$this, 'tag_close'))) {
			xml_parser_free ($this->parser);
			$this->error = 'SloppyDOM Error: Failed to set element handlers!';
			return false;
		}
		if (! xml_set_character_data_handler ($this->parser, array (&$this, 'cdata'))) {
			xml_parser_free ($this->parser);
			$this->error = 'SloppyDOM Error: Failed to set character data handler!';
			return false;
		}
		if (! xml_parser_set_option ($this->parser, XML_OPTION_CASE_FOLDING, false))  {
			xml_parser_free ($this->parser);
			$this->error = 'SloppyDOM Error: Failed to disable case folding!';
			return false;
		}
		if (! xml_set_default_handler ($this->parser, array (&$this, '_default'))) {
			xml_parser_free ($this->parser);
			$this->error = 'SloppyDOM Error: Failed to set default handler!';
			return false;
		}

		if ($this->parser) {
			$this->doc = new XMLDoc ();
			$this->comment = '';
			$this->parent = array (&$this->doc);
			if (xml_parse ($this->parser, $data, true)) {
				xml_parser_free ($this->parser);
				return $this->doc;
			} else {
				$this->err_code = xml_get_error_code ($this->parser);
				$this->err_line = xml_get_current_line_number ($this->parser);
				$this->err_byte = xml_get_current_byte_index ($this->parser);
				$this->err_colnum = xml_get_current_column_number ($this->parser);
				$this->error = 'SloppyDOM Error: ' . xml_error_string ($this->err_code);
				xml_parser_free ($this->parser);
				return false;
			}
		} else {
			$this->error = 'SloppyDOM Error: No parser available!';
			return false;
		}
	}

	/**
	 * This is the handler for new XML tags.
	 * 
	 * @access	private
	 * @param	resource	$parser
	 * @param	string	$tag
	 * @param	associative array	$attributes
	 * 
	 */
	function tag_open ($parser, $tag, $attributes) {
		if (strtolower (get_class ($this->parent[count ($this->parent) - 1])) == 'xmldoc') {
			// create the root node
			$node =& $this->parent[count ($this->parent) - 1]->addRoot ($tag);
		} else {
			// create a child node
			$node =& $this->parent[count ($this->parent) - 1]->addChild ($tag);
		}
		foreach ($attributes as $key => $value) {
			$node->setAttribute ($key, $value);
		}
		$node->comment = $this->comment;
		$this->comment = '';
		$this->parent[] =& $node;
	}

	/**
	 * This is the handler for new XML cdata - or content - blocks.
	 * 
	 * @access	private
	 * @param	resource	$parser
	 * @param	string	$cdata
	 * 
	 */
	function cdata ($parser, $cdata) {
		if ($this->comment_open) {
			$this->comment .= $cdata;
		} else {
			$this->parent[count ($this->parent) - 1]->content .= $cdata;
		}
	}

	/**
	 * This is the handler for closing XML tags.
	 * 
	 * @access	private
	 * @param	resource	$parser
	 * @param	string	$tag
	 * 
	 */
	function tag_close ($parser, $tag) {
		if (preg_match ("/^[\r\n\t ]*$/", $this->parent[count ($this->parent) - 1]->content)) {
			$this->parent[count ($this->parent) - 1]->content = '';
		}
		array_pop ($this->parent);
	}

	/**
	 * This is the handler for all things other than opening and closing tags
	 * and content blocks.  Right now it is only used to catch CDATA tags.
	 * 
	 * @access	private
	 * @param	resource	$parser
	 * @param	string	$data
	 * 
	 */
	function _default ($parser, $data) {
		if ($data == '<![CDATA[') {
			$this->parent[count ($this->parent) - 1]->cdata = true;
		} elseif (preg_match ("/<!--(.*)-->/s", $data, $regs)) {
			$this->comment = $regs[1];
		}
	}
}



?>