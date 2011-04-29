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
// XMLDocQuery provides XPath-compatible searching and referencing
// capabilities to the XMLDoc package.
//

/**
	 * XMLDocQuery provides XPath-compatible searching and referencing
	 * capabilities to the XMLDoc package.  Here is a list of supported Xpath
	 * features:
	 * 
	 * - /nodeName and //nodeName references
	 * - /nodeName[1] numbered references (counting starts from 1, not 0).
	 * - /nodeName[@attrName="attrValue"] references
	 * - /@attrName attribute references
	 * - /nodeName/@attrName = "some value" conditions
	 * 
	 * Features specifically not supported from the Xpath standard are:
	 * 
	 * - Axes, ie. child::someNodeName
	 * - Relative operators, ie. ./childNode, ../siblingNode, and *
	 * - Functions, ie. //item/title/upper-case(.)
	 * 
	 * For more information about Xpath, see http://www.w3.org/TR/xpath.
	 * 
	 * Query Examples
	 * --------------
	 * 
	 * (Based on an XHTML document structure.)
	 * 
	 * /html/body/h1 returns all h1 tags directly below the body tag.  Elements are
	 * returned as DocNode object references.
	 * 
	 * /html/body//p returns all p tags anywhere inside the body.
	 * 
	 * //p returns all p tags anywhere, even outside the body.
	 * 
	 * //a/@href returns all links in the document, as an array of DocAttr object
	 * references.
	 * 
	 * /html/body//p[1] returns the first p tag found inside the body.
	 * 
	 * //table/tr[1]/td[1] returns the first cell (top-left) of each table.
	 * 
	 * //map[@name="top-navbar"] returns the node <map name="top-navbar"></map>.
	 * 
	 * /html/body/h1 = "Welcome" returns a true or false value, depending on whether
	 * the first h1 tag contains the string "Welcome".
	 * 
	 * /html/head/meta returns all of the meta tags.
	 * 
	 * /html/head/meta[@name="description"] returns the meta description tag.
	 * 
	 * This class is not usually called directly, but can be accessed
	 * through the query() method of the XMLDoc and XMLNode classes.
	 * 
	 * New in 2.0:
	 * - Rewritten from scratch using the saf.Parser package.  Should be more
	 *   stable now and is more featureful now.
	 * - New features added: attribute references, conditions, and fixed the
	 *   numbebred references to begin counting from 1 instead of 0, which is
	 *   now compatible with the Xpath standard.
	 * 
	 * Historical:
	 * 
	 * New in 1.2:
	 * - Fixed a bug where the use of namespaces caused matching to fail.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $xq = new XMLDocQuery ($xhtml_doc);
	 * 
	 * $res = $xq->query ('/html/head/title');
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	XML
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.0, 2003-01-20, $Id: Query.php,v 1.4 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

$GLOBALS['loader']->import ('saf.Parser');

class XMLDocQuery extends Parser {
	/**
	 * List of currently selected nodes.
	 * 
	 * @access	private
	 * 
	 */
	var $nodes = array ();

	/**
	 * If a condition token is found, this is set to an array containing
	 * the 'operator' and the 'left' node set of the condition.
	 * 
	 * @access	private
	 * 
	 */
	var $condition;

	/**
	 * Set this to the current XMLDoc or XMLNode object.
	 * 
	 * @access	public
	 * 
	 */
	var $doc;

	/**
	 * If an error occurs, this will contain the message.
	 * 
	 * @access	public
	 * 
	 */
	var $error = false;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * 
	 */
	function XMLDocQuery () {
		//$this->addInternal ('', '\/\/@[a-zA-Z0-9:_-]+', false);
		$this->addInternal ('_attribute', '(\/)@([a-zA-Z0-9:_-]+)', false);
		$this->addInternal ('_nodeNum', '\/\/[a-zA-Z0-9:_-]+\[[0-9]+\]', false);
		$this->addInternal ('_nodeNum', '\/[a-zA-Z0-9:_-]+\[[0-9]+\]', false);
		$this->addInternal ('_nodeAttr', '\/\/[a-zA-Z0-9:_-]+\[@[a-zA-Z0-9:_-]+=".*"\]', false);
		$this->addInternal ('_nodeAttr', '\/[a-zA-Z0-9:_-]+\[@[a-zA-Z0-9:_-]+=".*"\]', false);
		$this->addInternal ('_node', '\/\/[a-zA-Z0-9:_-]+', false);
		$this->addInternal ('_node', '\/[a-zA-Z0-9:_-]+', false);
		$this->addInternal ('_condition', '[ \t\n\r]+!=|<=|>=|==|=|<|>[ \t\n\r]+', false);
		$this->addInternal ('_literal', '[0-9-]+|[\'"]{1}.*[\'"]{1}', false);
	}

	/**
	 * Executes a search against the XML tree structure using the
	 * specified $query.  Usually returns an array of matching nodes, either
	 * XMLDocNode or XMLDocAttr objects, or a boolean value on conditional
	 * queries.
	 * 
	 * @access	public
	 * @param	object reference	$rootNode
	 * @param	string	$query
	 * @return	mixed
	 * 
	 */
	function &query (&$rootNode, $query) {
		$xp = $this;
		unset ($xp->condition);
		unset ($xp->doc);
		unset ($xp->nodes);
		$xp->nodes = array ();
		$xp->nodes[] =& $rootNode;
		$xp->doc =& $rootNode;

		$xp->error = false;
		$xp->parse ($query);

		if ($xp->error !== false) {
			return false;
		} else {
			if ($xp->condition) {
				return $xp->evalCond ($xp->condition['operator'], $xp->condition['left'], $xp->nodes);
			}
			if (count ($xp->nodes) > 0) {
				return $xp->nodes;
			}
			return false;
		}
	}

	/**
	 * Executes a search against the XML tree structure in the
	 * specified $file, using the specified $query.  Returns the output
	 * of calling query() on the parsed file.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @param	string	$query
	 * @return	mixed
	 * 
	 */
	function queryFile ($file, $query) {
		global $loader;
		$loader->import ('saf.XML.Sloppy');
		$sloppy = new SloppyDOM;
		$doc = $sloppy->parseFromFile ($file);
		if (! $doc) {
			$this->error = $sloppy->error;
			$this->err_line = $sloppy->err_line;
			$this->err_byte = $sloppy->err_byte;
			$this->err_code = $sloppy->err_code;
			$this->err_colnum = $sloppy->err_colnum;
			return false;
		}
		return $this->query ($doc, $query);
	}

	/**
	 * Evaluates a condition specified by the $op operator,
	 * and the two sides to compare.
	 * 
	 * @access	public
	 * @param	string	$op
	 * @param	string	$one
	 * @param	string	$two
	 * @return	boolean
	 * 
	 */
	function evalCond ($op, $one, $two) {
		if (is_array ($one)) {
			$one = $one[0];
		}
		if (is_object ($one)) {
			if (strtolower (get_class ($one)) == 'xmlnode') {
				$one = $one->content;
			} elseif (strtolower (get_class ($one)) == 'xmlattr') {
				$one = $one->value;
			}
		}
		if (is_array ($two)) {
			$original_two = $two;
			$two = $two[0];
		}
		if (is_object ($two)) {
			if (strtolower (get_class ($two)) == 'xmlnode') {
				$two = $two->content;
			} elseif (strtolower (get_class ($two)) == 'xmlattr') {
				$two = $two->value;
			} elseif (strtolower (get_class ($two)) == 'xmldoc' && isset ($original_two[1])) {
				$two = $original_two[1];
			}
		}
		if ($op == '=') {
			$op = '==';
		}

		//echo htmlentities (CLOSE_TAG . OPEN_TAG . ' return (\'' . $one . '\' ' . $op . ' \'' . $two . '\'); ' . CLOSE_TAG) . "\n";
		return @eval (CLOSE_TAG . OPEN_TAG . ' return (\'' . $one . '\' ' . $op . ' \'' . $two . '\'); ' . CLOSE_TAG);
	}

	/**
	 * Evaluates whether the current $node matches the
	 * name and/or qualifications in $struct, which is created
	 * by the various callback methods.
	 * 
	 * @access	public
	 * @param	object reference	$node
	 * @param	string	$struct
	 * @return	boolean
	 * 
	 */
	function evaluate (&$node, $struct) {
		if (is_string ($struct) && $node->name == $struct) {
			return true;
		} elseif ($node->name == $struct['name']) {
			if (isset ($struct['attrName']) && $node->attributes[$struct['attrName']]->value == $struct['attrValue']) {
				return true;
			} elseif (isset ($struct['number']) && $node->number == $struct['number'] - 1) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Searches (optionally recursively) through the $nodes list,
	 * calling evaluate() to determine whether each node matches the
	 * current piece of the query (specified by $name).
	 * 
	 * @access	public
	 * @param	object reference	$nodes
	 * @param	mixed	$name
	 * @param	boolean	$recursive
	 * @return	array
	 * 
	 */
	function &getNodes (&$nodes, $name, $recursive = false) {
		$newNodes = array ();

		if (! is_array ($nodes)) {
			$node =& $nodes;
			unset ($nodes);
			$nodes = array ($node);
		}

		foreach ($nodes as $key => $node) {
			if (strtolower (get_class ($node)) == 'xmldoc') { // if we're starting from the doc, check the root
				if ($this->evaluate ($nodes[$key]->root, $name)) {
					$newNodes[] = $nodes[$key]->root;
				}
				if ($recursive && count ($nodes[$key]->root->children) > 0) {
					$n =& $this->getNodes ($nodes[$key]->root, $name, $recursive);
					foreach ($n as $k => $v) {
						$newNodes[] = $n[$k];
					}
				}
			} elseif (strtolower (get_class ($node)) == 'xmlnode') { // if we're starting from a node, use its children
				foreach ($nodes[$key]->children as $ck => $child) {
					if ($this->evaluate ($nodes[$key]->children[$ck], $name)) {
						$newNodes[] = $nodes[$key]->children[$ck];
					}
					if ($recursive && count ($child->children) > 0) {
						$n =& $this->getNodes ($nodes[$key]->children[$ck], $name, $recursive);
						foreach ($n as $k => $v) {
							$newNodes[] = $n[$k];
						}
					}
				}
			} elseif (strtolower (get_class ($node)) == 'xmlattr') {
				//
			}
		}

		return $newNodes;
	}

	/**
	 * The default callback method.  Does nothing, and isn't normally
	 * called.
	 * 
	 * @access	private
	 * @param	string	$token
	 * @param	string	$name
	 * 
	 */
	function _default ($token, $name) {
		//echo '==' . htmlentities ($token) . "==\n";
		//$this->nodes[] =& $token;
	}

	/**
	 * The condition operator handling callback method.
	 * Initializes the $condition property.
	 * 
	 * @access	private
	 * @param	string	$token
	 * @param	string	$name
	 * 
	 */
	function _condition ($token, $name) {
		$this->condition = array (
			'operator' => ltrim (rtrim ($token)),
			'left' => $this->nodes,
		);
		unset ($this->nodes);
		$this->nodes = array (&$this->doc);
	}

	/**
	 * The literal number and string handling callback method.
	 * 
	 * @access	private
	 * @param	string	$token
	 * @param	string	$name
	 * 
	 */
	function _literal ($token, $name) {
		//echo 'LITERAL: ' . htmlentities ($token) . "\n";
		$this->nodes[] = ltrim (rtrim ($token, '\'"'), '\'"');
	}

	/**
	 * The attribute handling callback method.
	 * 
	 * @access	private
	 * @param	string	$token
	 * @param	string	$name
	 * 
	 */
	function _attribute ($token, $name, $regs) {
		$attName = substr ($token, 2);
		$newNodes = array ();
		foreach ($this->nodes as $key => $node) {
			if (isset ($node->attributes[$attName])) {
				$newNodes[] = $this->nodes[$key]->attributes[$attName];
			}
		}
		unset ($this->nodes);
		$this->nodes =& $newNodes;
	}

	/**
	 * The numbered node handling callback method.
	 * 
	 * @access	private
	 * @param	string	$token
	 * @param	string	$name
	 * 
	 */
	function _nodeNum ($token, $name) {
		preg_match ('/(\/\/|\/)([a-zA-Z0-9:_-]+)\[([0-9]+)\]/s', $token, $regs);
		if (strlen ($regs[1]) == 1) {
			$nodes =& $this->getNodes ($this->nodes, array (
				'name' => $regs[2],
				'number' => $regs[3],
			), false);
		} else {
			$nodes =& $this->getNodes ($this->nodes, array (
				'name' => $regs[2],
				'number' => $regs[3],
			), true);
		}
		unset ($this->nodes);
		$this->nodes =& $nodes;
	}

	/**
	 * The attribute-specified node handling callback method.
	 * 
	 * @access	private
	 * @param	string	$token
	 * @param	string	$name
	 * 
	 */
	function _nodeAttr ($token, $name) {
		preg_match ('/(\/\/|\/)([a-zA-Z0-9:_-]+)\[@([a-zA-Z0-9:_-]+)="(.*)"\]/s', $token, $regs);
		if (strlen ($regs[1]) == 1) {
			$nodes =& $this->getNodes ($this->nodes, array (
				'name' => $regs[2],
				'attrName' => $regs[3],
				'attrValue' => $regs[4],
			), false);
		} else {
			$nodes =& $this->getNodes ($this->nodes, array (
				'name' => $regs[2],
				'attrName' => $regs[3],
				'attrValue' => $regs[4],
			), true);
		}
		unset ($this->nodes);
		$this->nodes =& $nodes;
	}

	/**
	 * The ordinary node handling callback method.
	 * 
	 * @access	private
	 * @param	string	$token
	 * @param	string	$name
	 * 
	 */
	function _node ($token, $name) {
		preg_match ('/(\/\/|\/)([a-zA-Z0-9:_-]+)/s', $token, $regs);
		if (strlen ($regs[1]) == 1) {
			$nodes =& $this->getNodes ($this->nodes, $regs[2], false);
		} else {
			$nodes =& $this->getNodes ($this->nodes, $regs[2], true);
		}
		unset ($this->nodes);
		$this->nodes =& $nodes;
	}
}



?>