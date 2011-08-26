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

$GLOBALS['loader']->import ('saf.XML.Doc.Attr');

/**
	 * XMLNode stores all the XML nodes of your document.
	 * 
	 * New in 1.2:
	 * - New $cdata property so that it knows to wrap the contents properly
	 *   in the write () method.
	 * 
	 * New in 1.4:
	 * - Added method query (), which allows users to traverse a set of nodes more
	 *   easily and more legibly by using the most basic subset of XPath.  Currently
	 *   supports only the most basic syntax (/node1/node2/node3).
	 * 
	 * New in 1.6:
	 * - Added a space before the slash in self-closing tags, so as to be able to
	 *   work with cross-browser-compatible XHTML markup.
	 * 
	 * New in 1.8:
	 * - Added line breaks and tabs to the write() method, so that XMLDoc produces
	 *   legible XML output.  Set $level to -1 to signify no auto-indenting.
	 * 
	 * New in 2.0:
	 * - Added a makeObj() method, which is helpful in passing nodes around like
	 *   one would database query results.
	 * 
	 * New in 2.2:
	 * - Added a makeRefObj() method, which is similar to makeObj(), but takes
	 *   references to the document structure instead, so any modifications to the
	 *   object returned are actually being made to the XML structure as well.
	 * 
	 * New in 2.4:
	 * - makeObj() and makeRefObj() now handle multiple child nodes with the same
	 *   name by turning them into an array of their $content properties.
	 * 
	 * New in 2.6:
	 * - Added a new parameter to the query() method that lets you return an array
	 *   of references to the resulting nodes instead of copies.  This makes it
	 *   easier to use the query() method in conjunction with document updates.
	 * 
	 * New in 3.0:
	 * - Changed query() so that it uses saf.XML.Doc.Query instead of processing
	 *   the query itself.
	 * - Added a path() method which returns the unique path (as understood by
	 *   saf.XML.Doc.Query) to the current node.
	 * - Added a $parent property, which references the parent node (if not the
	 *   root node).
	 * - Added a $number property, which keeps track of the position of this node
	 *   within similarly-named children of its parent.
	 * - Added the makeMenu() and _makeMenu() methods, which turn the current node
	 *   and its children into a saf.GUI.Menu object, making it easy to display
	 *   a document as a hierarchy using templates.
	 * 
	 * New in 3.2:
	 * - Added 4 new properties, two new methods, and a lot of power!
	 *   New properties:
	 *   - $callbackStart
	 *   - $callbackEnd
	 *   - $callbackObject
	 *   - $propagateCallback
	 *   New methods:
	 *   - setCallback()
	 *   - propagateCallback()
	 *   These new pieces add the ability to easily define custom callback functions
	 *   to handle the starting and ending tags of a node, essentially implementing
	 *   a form of XML transformations in pure PHP (read: EASY).  For an example
	 *   of a simple callback usage, check out saf.XML.SLiP.Writer in saf/docs or
	 *   DocReader.  This example package implements an XML to SLiP conversion.
	 *   SLiP is a simplified markup language that can be used to express
	 *   hierarchies in the same way that XML can, but is much quicker to read
	 *   and write making it ideal for pseudocode applications.
	 * 
	 * New in 3.4:
	 * - Modified makeObj() so that empty child nodes would be set as properties
	 *   with a value of 'true' (boolean).  This change was not made to makeRefObj()
	 *   however, because it is more proper in that case to refer to the empty
	 *   contents of the node, which is often the purpose of the reference to begin
	 *   with.
	 * 
	 * New in 3.6:
	 * - Modified path() to start counting path numbers at 1 instead of 0.
	 * - Added an $xquery property, and modified query() to work with the new
	 *   saf.XML.Doc.Query package.
	 * 
	 * New in 3.8:
	 * - Added named aliases to each child, so that they may be referred to by
	 *   name for convenience.  For example: $doc->_html->_head->_title->content
	 *   or $doc->_html->_body->_h1->content).  Note the underscores,
	 *   used to prevent naming conflicts.  In the case where there are more than
	 *   one child of the same name, the _name alias is turned into a numbered
	 *   array.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $foo =& new XMLNode ('foo');
	 * $bar =& $foo->addChild ('bar', 'qwerty');
	 * $foobar =& $foo->addChild ('foobar');
	 * $foobar->setAttribute ('asdf', 'fdsa');
	 * echo $foo->write ();
	 * 
	 * --- Output:
	 * <foo><bar>qwerty</bar><foobar asdf="fdsa"/></foo>
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	XML
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	3.8, 2003-07-11, $Id: Node.php,v 1.4 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class XMLNode {
	/**
	 * Name of the node.
	 * 
	 * @access	public
	 * 
	 */
	var $name;

	/**
	 * Content of the node.
	 * 
	 * @access	public
	 * 
	 */
	var $content;

	/**
	 * Array of child node objects.
	 * 
	 * @access	public
	 * 
	 */
	var $children = array ();

	/**
	 * Array of attribute objects.
	 * 
	 * @access	public
	 * 
	 */
	var $attributes = array (); // array of XMLAttr objects

	/**
	 * Attach a comment to the node, which will appear as an XML comment
	 * tag above the element.
	 * 
	 * @access	public
	 * 
	 */
	var $comment;

	/**
	 * Notes whether the contents of this node should be displayed as
	 * a <![CDATA[ ... ]]> block.
	 * 
	 * @access	public
	 * 
	 */
	var $cdata = false;

	/**
	 * Contains the number of this node within its parent node.  Numbers
	 * only increase when more than one child has the same name.
	 * 
	 * @access	public
	 * 
	 */
	var $number = 0;

	/**
	 * Contains a reference to the parent of this node, or false if this
	 * is the root node of the document.
	 * 
	 * @access	public
	 * 
	 */
	var $parent = false;

	/**
	 * The function or method to use as a callback for the start tag
	 * of the current XML node.
	 * 
	 * @access	public
	 * 
	 */
	var $callbackStart = false;

	/**
	 * The function or method to use as a callback for the end tag
	 * of the current XML node.
	 * 
	 * @access	public
	 * 
	 */
	var $callbackEnd = false;

	/**
	 * The object that contains the methods listed in $callbackStart
	 * and $callbackEnd (if they are methods and not ordinary custom or built-in
	 * PHP functions).
	 * 
	 * @access	public
	 * 
	 */
	var $callbackObject = false;

	/**
	 * Whether or not to propagate callback settings to new child
	 * nodes upon their creation.  Defaults to false.
	 * 
	 * @access	public
	 * 
	 */
	var $propagateCallback = false;

	/**
	 * If query() has been called, this will contain the XMLDocQuery object.
	 * 
	 * @access	public
	 * 
	 */
	var $xquery;

	/**
	 * Constructor method.  $content is optional.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$content
	 * 
	 */
	function XMLNode ($name, $content = '', $number, &$parent) {
		$this->name = $name;
		$this->content = $content;
		$this->number = $number;
		if (strtolower (get_class ($parent)) == 'xmlnode') {
			$this->parent =& $parent;
		}
	}

	/**
	 * Creates a child node of the current element.
	 * $content is optional.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$content
	 * @return	resource
	 * 
	 */
	function &addChild ($name, $content = '') {
		// returns XMLNode object, stores internal pointer
		$count = 0;
		foreach ($this->children as $child) {
			if ($name == $child->name) {
				$count++;
			}
		}
		$obj = new XMLNode ($name, $content, $count, $this);

		if ($this->propagateCallback) {
			if ($this->callbackEnd) {
				$obj->setCallback ($this->callbackStart, $this->callbackEnd, $this->callbackObject);
			} else {
				$obj->setCallback ($this->callbackStart, $this->callbackObject);
			}
			$obj->propagateCallback = true;
		}

		$this->children[] = $obj;
		if (! isset ($this->{'_' . $obj->name})) {
			$this->{'_' . $obj->name} =& $this->children[count ($this->children) - 1];
		} elseif (! is_array ($this->{'_' . $obj->name})) {
			$tmp =& $this->{'_' . $obj->name};
			unset ($this->{'_' . $obj->name});
			$this->{'_' . $obj->name} = array ();
			$this->{'_' . $obj->name}[] =& $tmp;
			$this->{'_' . $obj->name}[] =& $this->children[count ($this->children) - 1];
		} else {
			$this->{'_' . $obj->name}[] =& $this->children[count ($this->children) - 1];
		}
		return $this->children[count ($this->children) - 1];
	}

	/**
	 * Adds an attribute to the current element.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$value
	 * 
	 */
	function setAttribute ($name, $value) {
		// returns nothing
		$attr = new XMLAttr ($name, $value);
		$this->attributes[$name] = $attr;
	}

	/**
	 * Sets the callback setting for the current node.  $startFunction
	 * and $endFunction are methods of an object, which must be provided
	 * by $obj.
	 * 
	 * @access	public
	 * @param	string	$startFunction
	 * @param	string	$endFunction
	 * @param	object reference	$obj
	 * @return	boolean
	 * 
	 */
	function setCallback ($startFunction, $endFunction = false, &$obj) {
		if (is_object ($obj)) {
			if (! method_exists ($obj, $startFunction)) {
				$this->error = sprintf ('Method "%s" does not exist for class "%s"!', $startFunction, get_class ($obj));
				return false;
			}
			if ($endFunction !== false && ! method_exists ($obj, $endFunction)) {
				$this->error = sprintf ('Method "%s" does not exist for class "%s"!', $endFunction, get_class ($obj));
				return false;
			}
			$this->callbackStart = $startFunction;
			$this->callbackEnd = $endFunction;
			$this->callbackObject =& $obj;

	/*	} elseif (is_object ($endFunction)) {
			if (! method_exists ($endFunction, $startFunction)) {
				$this->error = sprintf ('Method "%s" does not exist for class "%s"!', $startFunction, get_class ($endFunction));
				return false;
			}
			$this->callbackStart = $startFunction;
			$this->callbackObject =& $endFunction;
	*/
		} elseif ($endFunction) {
			if (! function_exists ($startFunction)) {
				$this->error = sprintf ('Function "%s" does not exist!', $startFunction);
				return false;
			}
			if (! function_exists ($endFunction)) {
				$this->error = sprintf ('Function "%s" does not exist!', $endFunction);
				return false;
			}
			$this->callbackStart = $startFunction;
			$this->callbackEnd = $endFunction;

		} else {
			if (! function_exists ($startFunction)) {
				$this->error = sprintf ('Function "%s" does not exist!', $startFunction);
				return false;
			}
			$this->callbackStart = $startFunction;
		}
		return true;
	}

	/**
	 * Propagates a callback setting to the current node and all of its
	 * child nodes as well.  Useful for setting a "default" callback setting
	 * which can then be overridden on a per-node basis, and for adding callbacks
	 * to documents which were recreated from a pre-existing data source.
	 * 
	 * @access	public
	 * @param	string	$startFunction
	 * @param	string	$endFunction
	 * @param	object reference	$obj
	 * @return	boolean
	 * 
	 */
	function propagateCallback ($startFunction, $endFunction = false, &$obj) {
		//echo 'propagating callback to node ' . $this->name . '...<br />';
		if (! $this->setCallback ($startFunction, $endFunction, $obj)) {
			echo $this->error;
			return false;
		}
		$this->propagateCallback = true;
		foreach (array_keys ($this->children) as $child) {
			if (! $this->children[$child]->propagateCallback ($startFunction, $endFunction, $obj)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Returns the XML for the current node, and calls its
	 * children's write method to do the same.
	 * 
	 * @access	public
	 * @param	integer	$level
	 * @return	string
	 * 
	 */
	function write ($level = 0) {

		if ($level >= 0) {
			$nextlevel = $level + 1;
		} else {
			$nextlevel = $level;
		}

		if ($this->callbackStart) {
			$res = '';
			if (is_object ($this->callbackObject)) {
				$out = call_user_func (array (&$this->callbackObject, $this->callbackStart), $this, $level);
				if ($out === false) {
					return false;
				}
				$res .= $out;
				foreach ($this->children as $child) {
					$out = $child->write ($nextlevel);
					if ($out === false) {
						return false;
					}
					$res .= $out;
				}
				if ($this->callbackEnd) {
					$out = call_user_func (array (&$this->callbackObject, $this->callbackEnd), $this, $level);
					if ($out === false) {
						return false;
					}
					$res .= $out;
				}
				return $res;
			} else {
				$out = call_user_func ($this->callbackStart, $this, $level);
				if ($out === false) {
					return false;
				}
				$res .= $out;
				foreach ($this->children as $child) {
					$out = $child->write ($nextlevel);
					if ($out === false) {
						return false;
					}
					$res .= $out;
				}
				if ($this->callbackEnd) {
					$out = call_user_func ($this->callbackEnd, $this, $level);
					if ($out === false) {
						return false;
					}
					$res .= $out;
				}
				return $res;
			}
		}

		if ($level >= 0) {
			$nl = "\n";
			if ($level > 0) {
				$nl2 = "\n";
			} else {
				$nl2 = '';
			}
			$tab = '';
			for ($i = 0; $i < $level; $i++) {
				$tab .= "\t";
			};
		} else {
			$nl = '';
			$nl2 = '';
			$tab = '';
		}

		if (! empty ($this->comment)) {
			$data = $nl2 . $tab . '<!-- ' . $this->comment . " -->$nl$tab<" . $this->name;
		} else {
			$data = $nl2 . $tab . '<' . $this->name;
		}

		$data = $data;

		foreach ($this->attributes as $attr) {
			$data .= ' ' . $attr->name . '="' . $attr->value . '"';
		}
		if (count ($this->children) > 0) {
			$data .= '>';
			foreach ($this->children as $child) {
				$data .= $child->write ($nextlevel);
			}
			if (! empty ($this->content)) {
				if ($this->cdata) {
					$data .= '<![CDATA[ ' . $this->content . ' ]]>';
				} else {
					$data .= $this->content;
				}
			}
			$data .= $tab . '</' . $this->name . ">\n";
		} elseif (! empty ($this->content)) {
			$data .= '>';
			if ($this->cdata) {
				$data .= '<![CDATA[ ' . $this->content . ' ]]>';
			} else {
				$data .= $this->content;
			}
			$data .= '</' . $this->name . ">\n";
		} else {
			$data .= " />\n";
		}
		return $data;
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
	 * @param	mixed	$path
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
	 * Returns the full saf.XML.Doc.Query-compatible path to the
	 * current node.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function path () {
		$parent =& $this->parent;
		if (is_object ($parent)) {
			$path = '/' . $this->name . '[' . ($this->number + 1) . ']';
			while (is_object ($parent)) {
				$path = '/' . $parent->name . '[' . ($parent->number + 1) . ']' . $path;
				$parent =& $parent->parent;
			}
			return $path;
		} else {
			return '/' . $this->name . '[1]';
		}
	}

	/**
	 * Renders a node as an object of type StdClass, which is handy
	 * for passing around the SAF libraries, such as saf.Template.  The child
	 * nodes of this node become properties of this new object, and their
	 * values are the $content property of each child node.  Attributes of
	 * this node are placed in an associative array property of the new
	 * object called $attrs.  If there is more than one child node of the
	 * same name, the corresponding property of the object will be an array
	 * of their $content properties.  If a child node is empty (ie.
	 * self-closing), it's value will be a boolean 'true', even in an array.
	 * 
	 * @access	public
	 * @return	object
	 * 
	 */
	function makeObj () {
		$obj = new StdClass;
		foreach ($this->children as $child) {
			if (! isset ($obj->{$child->name})) {
				if (count ($child->children) <= 0 && empty ($child->content)) {
					$obj->{$child->name} = true;
				} else {
					$obj->{$child->name} = $child->content;
				}
			} elseif (is_array ($obj->{$child->name})) {
				if (count ($child->children) <= 0 && empty ($child->content)) {
					$obj->{$child->name}[] = true;
				} else {
					$obj->{$child->name}[] = $child->content;
				}
			} else {
				if (count ($child->children) <= 0 && empty ($child->content)) {
					$obj->{$child->name} = array ($obj->{$child->name});
					$obj->{$child->name}[] = true;
				} else {
					$obj->{$child->name} = array ($obj->{$child->name});
					$obj->{$child->name}[] = $child->content;
				}
			}
		}
		$obj->attrs = array ();
		foreach ($this->attributes as $attr) {
			$obj->attrs[$attr->name] = $attr->value;
		}
		return $obj;
	}

	/**
	 * This method is almost identical to makeObj(), except that
	 * instead of the new object containing all of the values of the node's
	 * children and attributes, makeRefObj() simply makes references to
	 * their values, so when the resulting object is modified, you are
	 * essentially modifying the values inside the XML document structure.
	 * If there is more than one child node of the same name, the
	 * corresponding property of the object will be an array of references
	 * to their $content properties.  There is one other difference between
	 * this method and makeObj(), which is that empty nodes are not set as
	 * properties with a 'true' value, but rather as references to the
	 * empty contents.  To evaluate whether such a property is set, instead
	 * use the isset() PHP function.
	 * 
	 * @access	public
	 * @return	object reference
	 * 
	 */
	function &makeRefObj () {
		$obj = new StdClass;
		/*
		foreach ($this->children as $child) {
			$obj->{$child->name} =& $child->content;
		}
		$obj->attrs = array ();
		foreach ($this->attributes as $attr) {
			$obj->attrs[$attr->name] =& $attr->value;
		}
		*/
		for ($i = 0; $i < count ($this->children); $i++) {
			if (! isset ($obj->{$this->children[$i]->name})) {
				$obj->{$this->children[$i]->name} =& $this->children[$i]->content;
			} elseif (is_array ($obj->{$this->children[$i]->name})) {
				$obj->{$this->children[$i]->name}[] =& $this->children[$i]->content;
			} else {
				$tmp =& $obj->{$this->children[$i]->name};
				unset ($obj->{$this->children[$i]->name});
				$obj->{$this->children[$i]->name} = array (&$tmp);
				$obj->{$this->children[$i]->name}[] =& $this->children[$i]->content;
			}
		}
		$obj->attrs = array ();
		for ($i = 0; $i < count ($this->attributes); $i++) {
			$obj->attrs[$this->attributes[$i]->name] =& $this->attributes[$i]->value;
		}
		return $obj;
	}

	/**
	 * Turns the current node into a menu item and calls its
	 * children to do the same.  Note: This method adds several new
	 * properties to each MenuItem object, which are: $content holds
	 * the $content of this node, $path holds the path() of this node,
	 * $encoded_path holds a urlencode()-ed copy of $path, and each
	 * attribute in $attributes becomes a property of the new MenuItem.
	 * Care must be taken though, or these can potentially overwrite
	 * important existing properties of the MenuItem object, such as
	 * its $id, $title, $parent, $children, or $colours.
	 * 
	 * @access	private
	 * @param	object reference	$menu
	 * @param	string	$parent
	 * @return	object
	 * 
	 */
	function _makeMenu (&$menu, $parent = '') {

		$id = $this->name . md5 (mt_rand (0,9999999));

		if (! empty ($parent)) {
			$menu->addItem ($id, $this->name, $parent);
			$item =& $menu->{'items_' . $id};
		} else {
			$menu->addItem ($id, $this->name);
			$item =& $menu->{'items_' . $id};
		}

		// the content, path, encoded_path, and attributes of the xml node
		// become properties of the item object, accessible through the
		// menu templates.
		$item->content =& $this->content;
		$item->path = $this->path ();
		$item->encoded_path = urlencode ($item->path);
		foreach ($this->attributes as $attr) {
			$item->{$attr->name} =& $attr->value;
		}

		foreach ($this->children as $child) {
			$child->_makeMenu ($menu, $id);
		}
		return $menu;
	}

	/**
	 * Creates a new saf.GUI.Menu object and turns this node and its
	 * children into items in that menu.  Returns the menu object.
	 * 
	 * @access	public
	 * @return	object
	 * 
	 */
	function makeMenu () {
		global $loader;
		$loader->import ('saf.GUI.Menu');
		$menu = new Menu ();
		return $this->_makeMenu ($menu);
	}
}



?>