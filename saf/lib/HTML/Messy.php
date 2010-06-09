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
// Messy is a package that parses and cleans invalid HTML markup.
//

loader_import ('pear.XML.HTMLSax3');

/**
 * Messy is a package that parses and cleans invalid HTML markup.
 *
 * @package HTML
 */
class Messy extends XML_HTMLSax3 {
	

	/**
	 * The output from the last call to parse().
	 * 
	 * @access	public
	 * 
	 */
	var $output = array ();

	/**
	 * Contains a list of tags that are self-closing (ie.
	 * they do not contain any data, such as a br tag).
	 * 
	 * @access	public
	 * 
	 */
	var $selfClosing = array (
		'img',
		'br',
		'hr',
		'meta',
		'link',
		'area',
	);

	/**
	 * Contains a list of tags that should be stripped
	 * from the output.
	 * 
	 * @access	public
	 * 
	 */
	var $stripTags = array (
		'font',
		'spacer',
		'blink',
		'xml:namespace',
		'o:p',
		'st1:city',
		'st1:address',
		'st1:street',
		'st1:state',
		'st1:place',
		'st1:placename',
		'st1:placetype',
		'st1:personname',
		'st1:country-region',
		'v:shapetype',
		'span',
		'del',
		'frame',
		'frameset',
		'layer',
		'ilayer',
		'link',
		'meta',
		'xml',
		'minmax_bound',
		'place',
		'placename',
		'placetype',
		'city',
		'state',
		'street',
		'personname',
		'country-region',
	);

	/**
	 * Contains a list of tags that should be stripped
	 * from the output.
	 * 
	 * @access	public
	 * 
	 */
	var $stripTagsSafe = array (
		'font',
		'spacer',
		'blink',
		'xml:namespace',
		'o:p',
		'st1:city',
		'st1:address',
		'st1:street',
		'st1:state',
		'st1:place',
		'st1:placename',
		'st1:placetype',
		'st1:personname',
		'st1:country-region',
		'v:shapetype',
		'span',
		'del',
		'script',
		'applet',
		'object',
		'iframe',
		'frame',
		'frameset',
		'layer',
		'ilayer',
		'embed',
		'bgsound',
		'link',
		'meta',
		'xml',
		'minmax_bound',
		'place',
		'placename',
		'placetype',
		'city',
		'state',
		'street',
		'personname',
		'country-region',
	);

	/**
	 * Contains a list of attributes that should be stripped
	 * from the output.
	 *
	 * @access	public
	 *
	 */
	var $stripAttrs = array (
	);

	/**
	 * Contains a list of attributes that should be stripped
	 * from the output.
	 *
	 * @access	public
	 *
	 */
	var $stripAttrsSafe = array (
		'onclick',
		'onsubmit',
		'onselect',
		'onchange',
		'onmouseover',
		'onmouseout',
		'onfocus',
		'onblur',
		'ondblclick',
		'onhelp',
		'onkeydown',
		'onkeypress',
		'onkeyup',
		'onmousedown',
		'onmousemove',
		'onmouseup',
		'onresize',
		'dataformatas',
		'data',
		'datafld',
		'datasrc',
		'dynsrc',
	);

	/**
	 * Contains a list of tags that should be transformed
	 * into other tags in the output.
	 * 
	 * @access	public
	 * 
	 */
	var $transform = array (
		'b' => 'strong', // direct switches can just list the new tag name
		'i' => 'em',
		'center' => array ( // array allows you to set attributes on transformations
			'tag' => 'div',
			'attrs' => array (
				'align' => 'center',
			),
		),
	);

	/**
	 * This array is used to compare opening and closing
	 * tags within the document structure, and to try to repair
	 * them by inserting missing tags where necessary.
	 * 
	 * @access	public
	 * 
	 */
	var $levels = array ();

	/**
	 * This tells Messy whether to use the stripTags and stripAttrs lists
	 * or the stripTagsSafe and stripAttrsSafe lists, which contain
	 * additional tags and attributes that are considered potentially
	 * unsafe.  The default is to use the latter and be more secure by
	 * default.
	 *
	 * @access	public
	 *
	 */
	var $safe = true;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * 
	 */
	function Messy () {
		$this->XML_HTMLSax3 ();
		$this->level = 0;
	}

	/**
	 * Parses the given HTML or XML $data into an array of
	 * "tokens", which are associative arrays with the following
	 * properties: tag (the name of the tag), attributes (a key/value
	 * array of tag attributes/properties), level (the depth of this
	 * tag within the document), type (either 'open', 'complete'
	 * - as in self-closing, 'cdata' - as in Character DATA, or
	 * 'close'), and the value of the tag (AKA the contents of it).
	 * This is also stored in the $output property of your Messy
	 * object.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @param	boolean	$isXml
	 * @return	array
	 * 
	 */
	function parse ($data, $isXml = false) {
		$this->set_object ($this);
		$this->set_element_handler ('handle_start_tag', 'handle_end_tag');
		$this->set_data_handler ('handle_data');
		$this->set_escape_handler ('handle_comment');
		$this->set_option ('XML_OPTION_TRIM_DATA_NODES', 0);

		$this->output = array ();
		$this->level = 0;

		$data = str_replace ('<?xml:', '<xml:', $data);

		if ($isXml) {
			if ($this->safe) {
				$strip = $this->stripTagsSafe;
				$this->stripTagsSafe = array ();
			} else {
				$strip = $this->stripTags;
				$this->stripTags = array ();
			}
			$close = $this->selfClosing;
			$this->selfClosing = array ();
			$trans = $this->transform;
			$this->transform = array ();
			$this->isXml = true;
		} else {
			$this->isXml = false;
		}

		if (strpos ($data, '<span id="xed-template">') !== false) {
			$data = preg_replace ('/<span id="xed-template">(.*?)<\/span>/', '\1', $data);
		}
		if (strpos ($data, '<span class="Apple-style-span"') !== false) {
			$data = preg_replace ('/<span class="Apple-style-span"([^>]*?)>(.*?)><\/span>/', '\2', $data);
		}
		while (preg_match ('|<pre>([^<]*)<br />|s', $data)) {
			$data = preg_replace ('|<pre>([^<]*)<br />|s', '<pre>\1', $data);
		}

		parent::parse ($data);

		// this block handles missing closing tags
		foreach (array_reverse ($this->levels) as $tag) {
			$this->level--;
			$this->output[] = array (
				'tag' => $tag,
				'attributes' => array (),
				'level' => $this->level,
				'type' => 'close',
				'value' => '',
			);
		}

		if ($isXml) {
			if ($this->safe) {
				$this->stripTagsSafe = $strip;
			} else {
				$this->stripTags = $strip;
			}
			$this->selfClosing = $close;
			$this->transform = $trans;
			$this->isXml = false;
		}

		return $this->output;
	}

	/**
	 * The internal character data handler.
	 * 
	 * @access	private
	 * @param	string	$data
	 * 
	 */
	function handle_data (&$parser, $data) {
		//if (in_array ($this->output[count ($this->output) - 1]['type'], array ('open', 'data'))) {
		if ($this->output[count ($this->output) - 1]['type'] == 'open') {
			$this->output[count ($this->output) - 1]['value'] .= $data;
		} else {
			$this->output[] = array (
				'tag' => false,
				'attributes' => array (),
				'level' => $this->level,
				'type' => 'cdata',
				'value' => $data,
			);
		}
	}

	/**
	 * The internal comment handler.
	 * 
	 * @access	private
	 * @param	string	$data
	 * 
	 */
	function handle_comment (&$parser, $data) {
		//if (in_array ($this->output[count ($this->output) - 1]['type'], array ('open', 'data'))) {
		if (strpos (strtolower ($data), 'doctype') === 0) {
			return $this->handle_doctype ($parser, $data);
		}
		$this->output[] = array (
			'tag' => false,
			'attributes' => array (),
			'level' => $this->level,
			'type' => 'comment',
			'value' => $data,
		);
	}

	/**
	 * The internal doctype handler.
	 * 
	 * @access	private
	 * @param	string	$data
	 * 
	 */
	function handle_doctype (&$parser, $data) {
		//if (in_array ($this->output[count ($this->output) - 1]['type'], array ('open', 'data'))) {
		$this->output[] = array (
			'tag' => false,
			'attributes' => array (),
			'level' => $this->level,
			'type' => 'doctype',
			'value' => $data,
		);
	}

	/**
	 * The open and complete tag handler.
	 * 
	 * @access	private
	 * @param	string	$tag
	 * @param	associative array	$attrs
	 * 
	 */
	function handle_start_tag (&$parser, $tag, $attrs = array ()) {
		$tag = trim ($tag);
		if (! $this->isXml) {
		    $tag = strtolower ($tag);

		    foreach ($attrs as $key => $val) {
				$new_key = strtolower ($key);
				if ($new_key == 'class' && strpos ($val, 'Mso') === 0) {
		    		unset ($attrs[$key]);
		    		continue;
		    	//} elseif ($tag != 'box' && $tag != 'form' && $tag != 'xt:box' && $tag != 'xt:form' && $new_key == 'style') {
		    	//	unset ($attrs[$key]);
		    	//	continue;
		    	} elseif (in_array ($new_key, $this->stripAttrs)) {
		    		unset ($attrs[$key]);
		    		continue;
		    	} elseif ($this->safe && (strpos ($val, 'javascript:') !== false && $val != 'javascript: history.go (-1)') || strpos ($val, 'vbscript:') !== false || strpos ($val, 'about:') !== false) {
		    		unset ($attrs[$key]);
		    		continue;
		    	}
				unset ($attrs[$key]);
				$attrs[$new_key] = $val;
		    }
		}

		if ($this->safe) {
			if (in_array ($tag, $this->stripTagsSafe)) {
				return;
			}
			if (strpos ($tag, 'w:') === 0) {
				return;
			}
		} else {
			if (in_array ($tag, $this->stripTags)) {
				return;
			}
			if (strpos ($tag, 'w:') === 0) {
				return;
			}
		}

		if (in_array ($tag, array_keys ($this->transform))) {
			if (is_array ($this->transform[$tag])) {
				foreach ($this->transform[$tag]['attrs'] as $key => $val) {
					$attrs[$key] = $val;
				}
				$tag = $this->transform[$tag]['tag'];
			} else {
				$tag = $this->transform[$tag];
			}
		}
		if (! in_array ($tag, $this->selfClosing)) {
			$this->output[] = array (
				'tag' => $tag,
				'attributes' => $attrs,
				'level' => $this->level,
				'type' => 'open',
				'value' => '',
			);
			$this->levels[$this->level] = $tag;
			$this->level++;
		} else {
			$this->output[] = array (
				'tag' => $tag,
				'attributes' => $attrs,
				'level' => $this->level,
				'type' => 'complete',
				//'value' => false,
			);
		}
	}

	/**
	 * The close tag handler.
	 * 
	 * @access	private
	 * @param	string	$tag
	 * 
	 */
	function handle_end_tag (&$parser, $tag) {
		$tag = trim ($tag);
		if (! $this->isXml) {
		    $tag = strtolower ($tag);
		}
		if ($this->safe) {
			if (in_array ($tag, $this->stripTagsSafe) || in_array ($tag, $this->selfClosing)) {
				return;
			}
			if (strpos ($tag, 'w:') === 0) {
				return;
			}
		} else {
			if (in_array ($tag, $this->stripTags) || in_array ($tag, $this->selfClosing)) {
				return;
			}
			if (strpos ($tag, 'w:') === 0) {
				return;
			}
		}
		if (in_array ($tag, array_keys ($this->transform))) {
			if (is_array ($this->transform[$tag])) {
				$tag = $this->transform[$tag]['tag'];
			} else {
				$tag = $this->transform[$tag];
			}
		}
		$this->level--;

		// this block handles missing closing tags
		while ($this->level > 0 && $this->levels[count ($this->levels) - 1] != $tag) {
			$this->output[] = array (
				'tag' => $this->levels[count ($this->levels) - 1],
				'attributes' => array (),
				'level' => $this->level,
				'type' => 'close',
				'value' => '',
			);
			array_pop ($this->levels);
			$this->level--;
		}

		unset ($this->levels[$this->level]);
		$this->output[] = array (
			'tag' => $tag,
			'attributes' => array (),
			'level' => $this->level,
			'type' => 'close',
			'value' => '',
		);
	}

	/**
	 * Returns a string of empty space, whose length
	 * is determined by the $length parameter.
	 * 
	 * @access	public
	 * @param	integer	$length
	 * @return	string
	 * 
	 */
	function pad ($length) {
		return str_pad ('', $length, ' ');
	}

	/**
	 * Uses the internal $output array from a previous
	 * call to parse() and returns an XML representation of
	 * the document.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function toXML () {
		$out = '';
		foreach ($this->output as $node) {
			if ($node['type'] == 'open') {
				$out .= '<' . $node['tag'];
				if (is_array ($node['attributes'])) {
					foreach ($node['attributes'] as $key => $value) {
						$out .= ' ' . $key . '="' . htmlentities ($value) . '"';
					}
				}
				$out .= ">" . $node['value'];
			} elseif ($node['type'] == 'complete') {
				$out .= '<' . $node['tag'];
				if (is_array ($node['attributes'])) {
					foreach ($node['attributes'] as $key => $value) {
						$out .= ' ' . $key . '="' . htmlentities ($value) . '"';
					}
				}
				$out .= ' />';
			} elseif ($node['type'] == 'close') {
				$out .= '</' . $node['tag'] . '>';
			} elseif ($node['type'] == 'cdata') {
				$out .= $node['value'];
			} elseif ($node['type'] == 'comment') {
				$out .= '<!--' . $node['value'] . "-->";
			} elseif ($node['type'] == 'doctype') {
				$out .= '<!' . $node['value'] . ">";
			}
		}

		$out = str_replace ('<xml:', '<?xml:', $out);

		return $out;
	}
	/*function toXML () {
		$out = '';
		foreach ($this->output as $node) {
			if ($node['type'] == 'open') {
				$out .= $this->pad ($node['level'] * 2) . '<' . $node['tag'];
				if (is_array ($node['attributes'])) {
					foreach ($node['attributes'] as $key => $value) {
						$out .= ' ' . $key . '="' . htmlentities ($value) . '"';
					}
				}
				$out .= ">\n" . $this->pad (($node['level'] + 1) * 2) . $node['value'] . "\n";
			} elseif ($node['type'] == 'complete') {
				$out .= $this->pad ($node['level'] * 2) . '<' . $node['tag'];
				if (is_array ($node['attributes'])) {
					foreach ($node['attributes'] as $key => $value) {
						$out .= ' ' . $key . '="' . htmlentities ($value) . '"';
					}
				}
				$out .= ' />' . "\n";
			} elseif ($node['type'] == 'close') {
				$out .= $this->pad ($node['level'] * 2) . '</' . $node['tag'] . '>' . "\n";
			} elseif ($node['type'] == 'cdata') {
				$out .= $this->pad ($node['level'] * 2) //. '<p>'
					. "\n" . $this->pad (($node['level'] + 1) * 2) . $node['value']
					. "\n" . $this->pad ($node['level'] * 2) //. '</p>'
					. "\n";
			} elseif ($node['type'] == 'comment') {
				$out .= $this->pad ($node['level'] * 2) . '<!--' . $node['value'] . "-->\n";
			} elseif ($node['type'] == 'doctype') {
				$out .= $this->pad ($node['level'] * 2) . '<!' . $node['value'] . ">\n";
			}
		}
		return $out;
	}*/

	/**
	 * Returns a "clean" version of the HTML or XML data
	 * provided, by calling both parse() then toXML() for you
	 * and return the result.
	 * 
	 * @access	public
	 * @param	string	$doc
	 * @param	boolean	$isXml
	 * @return	string
	 * 
	 */
	function clean ($doc, $isXml = false) {
		$this->parse ($doc, $isXml);
		return $this->toXML ();
	}

	/**
	 * Uses the internal $output array from a previous
	 * call to parse() and returns an XMLDoc object representation
	 * of the document.  Sets $error, $err_code, $err_line, etc.
	 * from the SloppyDOM error values and returns false should
	 * an error occur, which it easily could because there's
	 * no guarantee "cleaned up" markup is necessarily correctly
	 * formatted markup.
	 * 
	 * @access	public
	 * @return	object reference
	 * 
	 */
	function &toXMLDoc () {
		global $loader;
		$loader->import ('saf.XML.Sloppy');

		$sloppy = new SloppyDOM ();

		$xml = $this->toXML ();

		$xml = preg_replace ('/&([a-zA-Z0-9]+);/s', '<ch:\1 />', $xml);

		$doc = $sloppy->parse ($xml);

		if (! $doc) {
			$this->error = $sloppy->error;
			$this->err_code = $sloppy->err_code;
			$this->err_byte = $sloppy->err_byte;
			$this->err_line = $sloppy->err_line;
			$this->err_colnum = $sloppy->err_colnum;
			return false;
		}
		return $doc;
	}
}



?>
