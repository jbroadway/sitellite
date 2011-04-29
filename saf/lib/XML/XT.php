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
// XT is an XML-based template engine.
//

$GLOBALS['loader']->import ('saf.Template.Transformation');
$GLOBALS['loader']->import ('saf.XML.XT.Expression');

define ('XT_DEFAULT_PREFIX', 'xt:');
define ('XT_POST_PREFIX', 'xa:');

/**
	 * XT is an XML-based template engine.  For reference information,
	 * see the {@link http://www.sitellite.org/index/news-app/section.Templates Templates
	 * category of the Sitellite.org articles}.
	 *
	 * Change History
	 *
	 * New in 1.2:
	 * - Added support for xmlchar tags as a means of displaying HTML entities that
	 *   are not supported by the XML spec, without having to declare an HTML doctype.
	 *   For more information see http://xmlchar.sf.net/ and
	 *   http://www.w3.org/TR/REC-html40/sgml/entities.html for a list of HTML
	 *   entities.
	 * - Added global functions as aliases of the main public methods.  These simply
	 *   call the methods on a global XT object named $tpl.  They are: template_xt(),
	 *   template_messy(), template_validate(), and template_wrap().
	 * - Passed the default object to the $intl->get() calls so that {tag}-style
	 *   substitutions can be made.
	 * - Added aliases xt:translate and xt:i18n that point to xt:intl.
	 * - Added aliases xt:elsif that points to xt:elseif.
	 * - Fixed a few bugs regarding xt:content and xt:replace attributes, and the
	 *   xt:condition, xt:if, xt:elseif, and xt:else tags.
	 *
	 * New in 1.4:
	 * - Added an xt:cache tag which allows you to cache pieces of a template for
	 *   improved performance.
	 * - Added an ignoreUntilLevel() method which controls the private
	 *   $_ignoreUntilLevel property.
	 * - Added the $cacheLocation, $cacheDuration, and $cacheCount properties to
	 *   work with the new xt:cache tag.
	 * - Added the $isHtml property to tell the xt:intl tag whether or not to add
	 *   an HTML span tag to its output.
	 *
	 * New in 1.6:
	 * - Added xt:comment and xt:note tags (xt:note is an alias to xt:comment), so
	 *   that comments can be added to templates that will be retained in the
	 *   rendered output (since normal xml comments are stripped by the
	 *   xml_parse_into_struct() function).  For comments that should not be
	 *   retained, please use ordinary xml comments.
	 * - Added start, end, and length as attributes of loop iterators.
	 * - Added _header_handler(), makeToc(), and support for automatic generation
	 *   of tables of contents for HTML content.  Also added the $toc and $buildToc
	 *   properties.
	 *
	 * New in 1.8:
	 * - Added the ability to call XT tags (attributes too?  not sure) using
	 *   <xt-tagname /> as well as with the <xt:tagname /> namespace.  This feature
	 *   helps when you want to render XT tags with CSS, since namespaces in CSS are
	 *   not supported by any browser (except Opera 7 apparently) at present.
	 *
	 * New in 2.0:
	 * - Added the ability to use inline expressions in any tag attribute, for example:
	 *   <a href="${site/prefix}/index/news"><xt:intl>News</xt:intl></a>
	 *   This drastically reduces the amount of code needed to implement common
	 *   expressions, and really increases the flexibility of the language.
	 * - Fixed a bug where XT string expressions that began with an inline expression
	 *   (for example: <h1 xt:content="string: ${site/domain} - welcome">welcome</h1>)
	 *   would cause the inline expression to disappear.
	 * - Changes to PHPShorthand have improved the stability of the php expression type,
	 *   especially in the area of respecting quoted strings.
	 *
	 * New in 2.2:
	 * - Fixed a bug where tags that contained no children and used the xt:condition
	 *   attribute would improperly set the $ignoreUntilLevel variable, causing
	 *   unpredictable rendering below.
	 *
	 * New in 2.4:
	 * - Added a new include type "virtual" which includes a relative URL from the
	 *   web site document root, allowing the inclusion of CGI scripts and other
	 *   types of dynamic content directly into the template.  This would be the
	 *   equivalent of the PHP code:
	 *
	 *   include ('http://www.example.com/cgi-bin/script_name.cgi');
	 *
	 * <code>
	 * <?php
	 *
	 * loader_import ('saf.XML.XT');
	 *
	 * $tpl = new XT ('inc/html', XT_DEFAULT_PREFIX);
	 *
	 * $tpldata = '';
	 *
	 * if ($tpl->validate ($tpldata)) {
	 *     echo $tpl->fill (
	 *         $tpldata,
	 *         array (
	 *             'foo' => 'Testing...',
	 *         )
	 *     );
	 * } else {
	 *     echo 'Error: ' $tpl->error . ' on line ' . $tpl->err_line;
	 * }
	 *
	 * ? >
	 * </code>
	 *
	 * @package	XML
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.2, 2003-09-28, $Id: XT.php,v 1.17 2008/03/09 18:46:06 lux Exp $
	 * @access	public
	 * 
	 */
class XT {
	

	/**
	 * The path to the template directory.
	 * 
	 * @access	public
	 * 
	 */
	var $path = '';

	/**
	 * The output of the current fill() call.
	 * 
	 * @access	public
	 * 
	 */
	var $output = '';
	//var $object;

	/**
	 * A cache for templates read from files, so if they are
	 * called a second or third time XT doesn't have to read them from
	 * the file system again.
	 * 
	 * @access	private
	 * 
	 */
	var $cache = array ();

	/**
	 * A cache of the node array of parsed templates.  Used to
	 * reduce the number of XML parsers that need to be executed during
	 * a template with loops and complex structures in it.
	 * 
	 * @access	private
	 * 
	 */
	var $nodeCache = array ();

	/**
	 * An internal structure built to buffer SQL command blocks
	 * prior to executing them.
	 * 
	 * @access	private
	 * 
	 */
	var $sql = array ();
	//var $log = array ();

	/**
	 * An internal structure built to buffer loop command blocks
	 * prior to executing them.
	 * 
	 * @access	private
	 * 
	 */
	var $loop = array ();

	/**
	 * An internal structure built to buffer condition command blocks
	 * prior to executing them.
	 * 
	 * @access	private
	 * 
	 */
	var $if = array ();
	//var $switch = array ();

	/**
	 * An internal structure built to store template blocks
	 * so that they can be reused later in the same or even another
	 * script (via an include call).
	 * 
	 * @access	private
	 * 
	 */
	var $block = array ();
	//var $grids = array ();

	/**
	 * Tell XT to stop output until the closing of a certain
	 * tag has been found.  This is managed via the ignoreUntilLevel()
	 * method.
	 * 
	 * @access	private
	 * 
	 */
	var $_ignoreUntilLevel = array ();

	/**
	 * Used by the condition loop and condition blocks to tell
	 * XT to continue buffering the loop or condition body until the
	 * proper closing tag has been found.
	 * 
	 * @access	private
	 * 
	 */
	var $openUntilLevel = false;

	/**
	 * Used to distinguish between xt:loop and xt:condition
	 * attributes by the $openUntilLevel logic.
	 * 
	 * @access	private
	 * 
	 */
	var $isLoop = false;

	/**
	 * The current XML namespace XT is looking for to find
	 * command tags and attributes.
	 * 
	 * @access	public
	 * 
	 */
	var $prefix = 'xt:';
	//var $postfix = 'xa:';

	/**
	 * Determines whether the template should be treated as
	 * HTML or a different kind of markup.  This affects tags such
	 * as xt:intl where an HTML span tag can be added to surround
	 * the string.
	 * 
	 * @access	public
	 * 
	 */
	var $isHtml = true;

	/**
	 * Contains a list of tags that are self-closing (ie.
	 * they do not contain any data, such as a br tag).
	 * This list is only referenced if $isHtml is true.
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
	 * Determines whether the template should build a
	 * table of contents (TOC) based on HTML header tags found within.
	 * Defaults to false, and must be set to true in order to
	 * generate TOCs.
	 * 
	 * @access	public
	 * 
	 */
	var $buildToc = false;

	/**
	 * The list of HTML headers found in the document.
	 * 
	 * @access	public
	 * 
	 */
	var $toc = array ();

	var $_addToHeader = false;

	var $_bind_list = array ();

	var $_bind_attrs = array ();

	var $_bind_parts = array ();

	/**
	 * Location to store cached contents in.  Defaults to
	 * 'store:cache/templates/'.  Note that the scope will be
	 * appended to the $cacheLocation for each cacheable element.
	 * 
	 * @access	public
	 * 
	 */
	var $cacheLocation = 'store:cache/templates/';

	/**
	 * How long in seconds to store the cached elements
	 * before regenerating them.  May be overridden with the duration
	 * attribute of the cache tag.  Defaults to 3600 which is one hour.
	 * 
	 * @access	public
	 * 
	 */
	var $cacheDuration = 3600;

	/**
	 * Used to generate an auto-incrementing ID value for
	 * cache elements that are missing an "id" attribute (so as
	 * to make the attribute optional).
	 * 
	 * @access	public
	 * 
	 */
	var $cacheCount = 0;

	/**
	 * Contains the name of the file currently being processed.
	 * This is set in the getDoc() method, so technically it will set
	 * the current file even when you're just retrieving its contents.
	 * This shouldn't affect its validity for most uses, but when you
	 * want to retrieve the last parsed file, it means you have to do
	 * so prior to calling getDoc() again, either directly or indirectly.
	 * 
	 * @access	public
	 * 
	 */
	var $file = false;

	/**
	 * The XTE object used to evaluate expressions in XT
	 * tags.
	 * 
	 * @access	public
	 * 
	 */
	var $exp;

	/**
	 * The XML parser resource.
	 * 
	 * @access	private
	 * 
	 */
	var $parser;

	/**
	 * The error message, if an error occurs.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * The error code, if an error occurs.
	 * 
	 * @access	public
	 * 
	 */
	var $err_code;

	/**
	 * The error byte number, if an error occurs.
	 * 
	 * @access	public
	 * 
	 */
	var $err_byte;

	/**
	 * The error line number, if an error occurs.
	 * 
	 * @access	public
	 * 
	 */
	var $err_line;

	/**
	 * The error column number, if an error occurs.
	 * 
	 * @access	public
	 * 
	 */
	var $err_colnum;

	/**
	 * Rows from an xt:sql statement.
	 *
	 * @access	public
	 *
	 */
	var $rows = 0;

	/**
	 * Transformations to perform on a variable.
	 *
	 * @access	public
	 *
	 */
	var $transformations = array ();

	/**
	 * Constructor method.  $prefix is either XT_DEFAULT_PREFIX,
	 * XT_POST_PREFIX, or a custom prefix.  The prefix is essentially
	 * the XML namespace XT is to recognize.  The use of multiple
	 * namespaces can allow you to partially parse a template, cache
	 * that, then parse the rest which might contain user-specific content
	 * such as personal information.  XT_DEFAULT_PREFIX and XT_POST_PREFIX
	 * are constants defined by this package.
	 * 
	 * @access	public
	 * @param	string	$path
	 * @param	string	$prefix
	 * 
	 */
	function XT ($path = '', $prefix = XT_DEFAULT_PREFIX) {
		$this->path = $path;
		$this->prefix = $prefix;
		$this->exp = new XTExpression (false);
	}

	/**
	 * Retrieves a template from the appropriate location,
	 * such as the $cache array, $nodeCache array, a file, or if
	 * the string is the template itself, it returns that.  Also
	 * handles caching to $cache and $nodeCache of the appropriate
	 * templates.
	 * 
	 * @access	private
	 * @param	string	$data
	 * @return	string
	 * 
	 */
	function getDoc ($data) {
		if (@is_array ($this->nodeCache[$data])) {
			return $this->nodeCache[$data];
		}

		$doc = $data;

		/*
		if (! empty ($this->path)) {
			$path = $this->path . '/';
		}
		*/
		$path = $this->path () . '/';

		// get real data if data is from a file
		if (@is_file ($path . $data)) {
			if (isset ($this->cache[$data])) {
				$data = $this->cache[$data];
			} else {
				$file = $data;
				$data = @join ('', @file ($path . $file));
				$this->cache[$file] = $data;
				$this->file = $file;
			} // else do nothing, data is a string already
		}
		return $data;
	}

	/**
	 * Returns either the contents of the $path property,
	 * or the current working directory, which should be used as
	 * the path instead.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function path () {
		if (! empty ($this->path)) {
			return $this->path;
		} else {
			return getcwd ();
		}
	}

	function ignoreUntilLevel ($level = false) {
		if ($level === -1) {
			array_pop ($this->_ignoreUntilLevel);
		} elseif ($level !== false) {
			$this->_ignoreUntilLevel[] = $level;
		} else {
			$c = count ($this->_ignoreUntilLevel);
			if ($c > 0) {
				return $this->_ignoreUntilLevel[$c - 1];
			} else {
				return false;
			}
		}
	}

	/**
	 * Validates a template to see if it is a valid XML
	 * document.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @return	boolean
	 * 
	 */
	function validate ($data) {
		$data = $this->getDoc ($data);

		if (is_array ($data)) { // if data came from nodeCache it must be valid
			return true;
		}

		// create the xml parser now, and declare the handler methods
		$this->parser = xml_parser_create ($this->encoding);
		if (! $this->parser) {
			$this->error = 'Template Error: Failed to create an XML parser!';
			return false;
		}
		if (! xml_parser_set_option ($this->parser, XML_OPTION_CASE_FOLDING, false))  {
			xml_parser_free ($this->parser);
			$this->error = 'Template Error: Failed to disable case folding!';
			return false;
		}

		if ($this->parser) {
			if (xml_parse ($this->parser, $data, true)) {
				xml_parser_free ($this->parser);
				return true;
			} else {
				$this->err_code = xml_get_error_code ($this->parser);
				$this->err_line = xml_get_current_line_number ($this->parser);
				$this->err_byte = xml_get_current_byte_index ($this->parser);
				$this->err_colnum = xml_get_current_column_number ($this->parser);
				$this->error = 'Template Error: ' . xml_error_string ($this->err_code);
				xml_parser_free ($this->parser);
				return false;
			}
		} else {
			$this->error = 'Template Error: No parser available!';
			return false;
		}
	}

	/**
	 * Executes a template.  $obj is an optional object you
	 * can pass to the template, which makes its properties immediately
	 * available to the template.  $carry is used internally to determine
	 * whether to reset the object register before executing.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @param	object	$obj
	 * @param	boolean	$carry
	 * @return	string
	 * 
	 */
	function fill ($data, $obj = '', $carry = false) {
		$this->error = false;

		// duplicate object for parser isolation
		$tpl = clone ($this); // deliberate copy, we want two separate objects here
		$tpl->exp = clone ($this->exp);
		if (! $carry) {
			//$tpl->register = array ();
			$tpl->exp->resetRegister ();
			$tpl->carry = false;
		} else {
			$tpl->carry = true;
			//$tpl->register = array ();
			//$tpl->register =& $this->register;
			//$this->exp->resetRegister ();
		}
		$tpl->output = '';
		if ($obj !== '') {
			//$tpl->register['object'] = $obj;
			$tpl->exp->setObject ($obj);
		} else {
			//$tpl->register['object'] = new StdClass;
			$tpl->exp->setObject (new StdClass);
		}
		$tpl->sql = array ();
		$tpl->loop = array ();
		$tpl->if = array ();
		$tpl->switch = array ();
		$tpl->buffer = array ();
		$tpl->open = false;
		$tpl->open_var = false;
		$tpl->toc = array ();
		$tpl->rows = 0;

		$tpl->error = false;
		$tpl->err_code = false;
		$tpl->err_byte = false;
		$tpl->err_line = false;
		$tpl->err_colnum = false;

		$doc = $data;
		$data = $tpl->getDoc ($data);

		if (is_array ($data)) { // use nodeCache instead of new xml parser
			foreach ($data as $node) {
				$node = $this->reverseEntities ($node);
				$tpl->_output ($tpl->{$tpl->makeMethod ($node['tag'], $node['type'], $node['level'])} ($node));
			}

			// gather blocks from included templates
			foreach ($tpl->block as $key => $block) {
				if (! isset ($this->block[$key])) {
					$this->block[$key] =& $tpl->block[$key];
				}
			}

			$this->rows = $tpl->rows;
			$this->toc = $tpl->toc;
			//$tpl->output = $this->reverseEntities ($tpl->output);
			return $tpl->output;
		}

		// create the xml parser now, and declare the handler methods
		$this->parser = xml_parser_create ($this->encoding);
		if (! $this->parser) {
			$this->error = 'Template Error: Failed to create an XML parser!';
			return false;
		}
		if (! xml_parser_set_option ($this->parser, XML_OPTION_CASE_FOLDING, false))  {
			xml_parser_free ($this->parser);
			$this->error = 'Template Error: Failed to disable case folding!';
			return false;
		}

		if ($this->parser) {
			// turning inline PHP off for the time being
			// actually, i don't think we need it at all
			//$data = $tpl->inline ($data);

			$data = $this->convertEntities ($data);

			if (xml_parse_into_struct ($this->parser, $data, $tpl->vals, $tpl->tags)) {
				xml_parser_free ($this->parser);

				//echo '<pre>';
				//print_r ($tpl->vals);
				//echo '</pre>';

				// cache the node structure
				$this->nodeCache[$data] = $tpl->vals;

				// list of paths for the current tag and its parents
				// takes the form [level] = [path 1, path 2]
				$this->_path_list = array ();
				// the current level
				$this->_path_level = 0;

/*
				$colours = array (
					'000',
					'600',
					'060',
					'006',
					'900',
					'396',
					'369',
					'f00',
					'0f0',
					'00f',
					'f90',
					'666',
					'999',
					'bbb',
				);
*/
				// mainloop
				foreach ($tpl->vals as $node) {
					$node = $this->reverseEntities ($node);
					$norm_tag = str_replace (':', '-', $node['tag']);
					if ($node['type'] == 'cdata' || strpos ($norm_tag, 'ch-') === 0 || strpos ($norm_tag, 'xt-') === 0 || ! in_array ($norm_tag, $tpl->_bind_parts)) {
						$tpl->_output ($tpl->{$tpl->makeMethod ($node['tag'], $node['type'], $node['level'])} ($node));
						continue;
					}
//					echo '<span style="color: #' . $colours[$node['level']] . '">' . str_repeat ('    ', $node['level']) . $node['tag'] . ' (' . $node['type'] . ")</span>\n";
					$node['paths'] = array ();
					if ($node['type'] == 'open' || $node['type'] == 'complete') {
						if ($node['level'] > $this->_path_level) {
							// moving up a new level (ie. a sub-node)
							$this->_path_level++;
							$this->_path_list[$this->_path_level] = $node;
						} elseif ($this->_path_level > 0 && $node['level'] == $this->_path_level) {
							// next sibling at the same level
							array_pop ($this->_path_list);
							$this->_path_list[$this->_path_level] = $node;
						} elseif ($node['level'] < $this->_path_level) {
							// do nothing...
						} else {
							// moving up a new level
							$this->_path_level++;
							$this->_path_list[$this->_path_level] = $node;
						}

						// compile all variations of this tag's xpath for a match in $this->_bind_list
						$paths = array ('//' . $node['tag']);
						$list = $this->_path_list[$this->_path_level - 2]['paths'];
						if (is_array ($list)) {
							foreach ($list as $p) {
								$paths[] = $p . '/' . $node['tag'];
								$paths[] = $p . '//' . $node['tag'];
							}
						} else {
							$paths[] = '/' . $node['tag'];
						}
						$count = count ($paths);
						$cpl = count ($this->_path_list) - 1;
						if (is_array ($this->_path_list[$cpl]['attributes'])) {
							foreach ($this->_path_list[$cpl]['attributes'] as $k => $v) {
								if (strpos ($k, 'xt:') !== 0) {
									for ($i = 0; $i < $count; $i++) {
										$paths[] = $paths[$i] . '[@' . $k . '="' . $v . '"]';
									}
								}
							}
						}
//						echo '<div style="padding: 10px; margin: 10px; border: 1px solid #aaa">' . join ("\n", $paths) . '</div>';
						$this->_path_list[$cpl]['paths'] = $paths;
						$node['paths'] = $paths;

						if ($node['type'] == 'complete') {
							foreach (array_intersect (array_keys ($this->_bind_list), $paths) as $key) {
								$node['value'] .= $this->_bind_list[$key];
							}
						}
						foreach (array_intersect (array_keys ($this->_bind_attrs), $paths) as $key) {
							//info ($node['attributes']);
							foreach ($this->_bind_attrs[$key] as $k => $v) {
								$node['attributes'][$k] = $v;
							}
							//info ($node['attributes']);
						}

						if ($node['type'] == 'complete') {
							$this->_path_level--;
							array_pop ($this->_path_list);
						}
					} elseif ($node['type'] == 'close') {
						if (count ($this->_path_list) > 0 && $this->_path_list[count ($this->_path_list) - 1] != null) {
							foreach (array_intersect (array_keys ($this->_bind_list), $this->_path_list[count ($this->_path_list) - 1]['paths']) as $key) {
								$tpl->_output ($this->_bind_list[$key]);
							}
							$this->_path_level--;
							array_pop ($this->_path_list);
						}
					}
					$tpl->_output ($tpl->{$tpl->makeMethod ($node['tag'], $node['type'], $node['level'])} ($node));
				}

				// gather blocks from included templates
				foreach ($tpl->block as $key => $block) {
					if (! isset ($this->block[$key])) {
						$this->block[$key] =& $tpl->block[$key];
					}
				}

				$this->rows = $tpl->rows;
				$this->toc = $tpl->toc;
				//$tpl->output = $this->reverseEntities ($tpl->output);
				return $tpl->output;

			} else {
				$this->err_code = xml_get_error_code ($this->parser);
				$this->err_line = xml_get_current_line_number ($this->parser);
				$this->err_byte = xml_get_current_byte_index ($this->parser);
				$this->err_colnum = xml_get_current_column_number ($this->parser);
				$this->error = 'Template Error: ' . xml_error_string ($this->err_code);
				xml_parser_free ($this->parser);
				return false;
			}
		} else {
			$this->error = 'Template Error: No parser available!';
			return false;
		}
	}

	/**
	 * Bind some content to the specified tag.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 *
	 */
	function bind ($path, $data) {
		$this->_bind_list[$path] .= $data;
		$path = preg_split ('|/+|', $path, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($path as $k => $v) {
			if (strpos ($v, '[') !== false) {
				$path[$k] = preg_replace ('|\[[^\]]+\]|', '', $v);
			}
		}
		$this->_bind_parts = array_unique (array_merge ($this->_bind_parts, $path));
	}

	/**
	 * Bind an attribute to the specified tag.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 *
	 */
	function bindAttr ($path, $attr, $value) {
		$this->_bind_attrs[$path][$attr] = $value;
		$path = preg_split ('|/+|', $path, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($path as $k => $v) {
			if (strpos ($v, '[') !== false) {
				$path[$k] = preg_replace ('|\[[^\]]+\]|', '', $v);
			}
		}
		$this->_bind_parts = array_unique (array_merge ($this->_bind_parts, $path));
	}

	/**
	 * Uses the saf.HTML.Messy package to implement a "messy"
	 * parser in XT, allowing for invalid markup in templates (ie.
	 * HTML instead of XHTML).  Of course since the markup accepted is
	 * invalid, your mileage may vary.  This method is discouraged
	 * unless you have a good reason for using it.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @param	object	$obj
	 * @param	boolean	$carry
	 * @return	string
	 * 
	 */
	function messy ($data, $obj = '', $carry = false) {
		if (! is_object ($this->messy)) {
			global $loader;
			$loader->import ('saf.HTML.Messy');
			$this->messy = new Messy ();
		}
		/*
		if (! empty ($this->path)) {
			$path = $this->path . '/';
		}
		*/
		$path = $this->path () . '/';
		if (@is_file ($path . $data)) {
			$data = $this->getDoc ($data);
		}
		$this->nodeCache[$data] = $this->messy->parse ($data);
		return $this->fill ($data, $obj, $carry);
	}

	/**
	 * Executes the specified box using the Sitellite box API,
	 * which is essentially just an include.  Note: This is now an alias
	 * for the loader_box() function.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	associative array	$parameters
	 * @return	string
	 * 
	 */
	function box ($name, $parameters = array ()) {
		if ($this->file) {
			$GLOBALS['_xte'] =& $this->exp;
		}
		$out = loader_box ($name, $parameters);
		unset ($GLOBALS['_xte']);
		if (empty ($out)) {
			return html_marker ('Empty Box: ' . $name);
		}
		return html_marker ('Box: ' . $name) . $out;
	}

	/**
	 * Executes the specified form using the Sitellite form API,
	 * which is essentially just an include of a file that defines a
	 * subclass of saf.MailForm.  Note: This is now an alias
	 * for the loader_form() function.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @return	string
	 * 
	 */
	function form ($name) {
		$out = loader_form ($name);
		if (empty ($out)) {
			return html_marker ('Empty Form: ' . $name);
		}
		return html_marker ('Form: ' . $name) . $out;
	}

	/**
	 * Evaluates PHP code embedded into a template.  Currently
	 * not used, because there's really no reason why embedded PHP
	 * should be needed.
	 * 
	 * @access	private
	 * @param	string	$data
	 * @return	string
	 * 
	 */
	function inline ($data) {
		ob_start ();
		eval (CLOSE_TAG . $data);
		$newdata = ob_get_contents ();
		ob_end_clean ();
		return $newdata;
	}

	/**
	 * Determines which callback function to call for the
	 * specified node.
	 * 
	 * @access	private
	 * @param	string	$name
	 * @param	string	$type
	 * @return	string
	 * 
	 */
	function makeMethod ($name, $type, $level) {
		$iul = $this->ignoreUntilLevel ();
		if ($iul && $iul < $level) {
			switch ($type) {
				case 'close':
					return '_default_end';
				case 'cdata':
					return '_default_cdata';
				default:
					return '_default';
			}
		}

		if (strpos ($name, 'xt-') === 0) {
			$name = str_replace ('xt-', 'xt:', $name);
		}

		switch ($type) {
			case 'complete':
				if (strpos ($name, 'ch:') === 0) {
					return '_ch_handler';
				}
			case 'open':
				if ($this->buildToc && in_array ($name, array ('h1', 'h2', 'h3', 'h4', 'h5', 'h6'))) {
					return '_header_handler';
				}
				$m = str_replace (array ($this->prefix, '-'), array ('_', '_'), $name);
				$d = '_default';
				break;
			case 'close':
				if ($this->buildToc && in_array ($name, array ('h1', 'h2', 'h3', 'h4', 'h5', 'h6'))) {
					return '_header_end';
				}
				$m = str_replace (array ($this->prefix, '-'), array ('_', '_'), $name) . '_end';
				$d = '_default_end';
				break;
			default:
				$m = str_replace (array ($this->prefix, '-'), array ('_', '_'), $name) . '_cdata';
				$d = '_default_cdata';
		}
		if (strpos ($m, '_') === 0 && method_exists ($this, $m)) {
			return $m;
		}
		return $d;
	}

	/**
	 * Determines where to send the output of a tag callback.
	 * 
	 * @access	private
	 * @param	string	$str
	 * 
	 */
	function _output ($str) {
		if ($this->open) {
			$this->open_var .= $str;
		} else {
			$this->output .= $str;
		}
	}

	/**
	 * Wraps $str in xt:tpl tags and returns it.
	 * 
	 * @access	public
	 * @param	string	$str
	 * @return	string
	 * 
	 */
	function wrap ($str) {
		return '<' . $this->prefix . 'tpl>' . $str . '</' . $this->prefix . 'tpl>';
	}

	/**
	 * Sets the value of a property of the default object
	 * in the register.  Always returns an empty string.
	 * 
	 * @access	private
	 * @param	string	$name
	 * @param	string	$value
	 * @return	string
	 * 
	 */
	function setVal ($name, $value) {
		if (is_object ($this->exp->register['object'])) {
			$this->exp->register['object']->{$name} = $value;
		} elseif (is_array ($this->exp->register['object'])) {
			$this->exp->register['object'][$name] = $value;
		} else {
			$this->exp->setObject (new StdClass);
			$this->exp->register['object']->{$name} = $value;
		}
		return '';
	}

	/**
	 * Gets the value of a property in the register.
	 * Returns that value, which may be transformed, if a
	 * transformation has been defined for that property name.
	 * 
	 * @access	private
	 * @param	string	$val
	 * @param	associative array	$node
	 * @param	string	$type
	 * @param	boolean	$transform
	 * @return	string
	 * 
	 */
	function getVal ($val, $node, $type = 'path', $transform = true) {
		if (preg_match ('/\/([a-zA-Z0-9_-]+)$/', $val, $regs)) {
			$var = $regs[1];
		} else {
			$var = $val;
		}
		$val = $this->exp->evaluate ($val, $node, $type, $this->carry);
		if ($transform && isset ($this->transformations[$var]) && is_object ($this->transformations[$var])) {
			return $this->transformations[$var]->transform ($val);
		} else {
			return $val;
		}
	}

	/**
	 * Reverses the conversion of HTML entities to XT-compatible ch:entity
	 * tags.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @return	string
	 * 
	 */
    function reverseEntities ($data) {
    	if (is_array ($data)) {
    		if (isset ($data['value'])) {
	    		$data['value'] = $this->reverseEntities ($data['value']);
	    	}
    		if (isset ($data['attributes'])) {
    			foreach ($data['attributes'] as $key => $value) {
    				$data['attributes'][$key] = $this->reverseEntities ($value);
    			}
    		}
    		return $data;
    	}
    	$data = preg_replace (
    		'/\[ch:n([0-9]+)\]/',
    		'&#\1;',
    		$data
    	);
    	return preg_replace (
    		'/\[ch:([a-zA-Z0-9]+)\]/',
    		'&\1;',
    		$data
    	);
    }

	/**
	 * Converts HTML entities into XT-compatible ch:entity
	 * tags.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @return	string
	 * 
	 */
    function convertEntities ($data) {
    	$data = preg_replace (
    		'/&#([0-9]+);/',
    		'[ch:n\1]',
    		$data
    	);
    	return preg_replace (
    		'/&([a-zA-Z0-9]+);/',
    		'[ch:\1]',
    		$data
    	);
		$data = preg_replace (
    		'/&#([0-9]+);/',
    		'<ch:n\1 />',
    		$data
    	);
    	return preg_replace (
    		'/&([a-zA-Z0-9]+);/',
    		'<ch:\1 />',
    		$data
    	);
        return str_replace (
            array (
				'&nbsp;',
				'&iexcl;',
				'&cent;',
				'&pound;',
				'&curren;',
				'&yen;',
				'&brvbar;',
				'&sect;',
				'&uml;',
				'&copy;',
				'&ordf;',
				'&laquo;',
				'&not;',
				'&shy;',
				'&reg;',
				'&macr;',
				'&deg;',
				'&plusmn;',
				'&sup2;',
				'&sup3;',
				'&acute;',
				'&micro;',
				'&para;',
				'&middot;',
				'&cedil;',
				'&sup1;',
				'&ordm;',
				'&raquo;',
				'&frac14;',
				'&frac12;',
				'&frac34;',
				'&iquest;',
				'&Agrave;',
				'&Aacute;',
				'&Acirc;',
				'&Atilde;',
				'&Auml;',
				'&Aring;',
				'&AElig;',
				'&Ccedil;',
				'&Egrave;',
				'&Eacute;',
				'&Ecirc;',
				'&Euml;',
				'&Igrave;',
				'&Iacute;',
				'&Icirc;',
				'&Iuml;',
				'&ETH;',
				'&Ntilde;',
				'&Ograve;',
				'&Oacute;',
				'&Ocirc;',
				'&Otilde;',
				'&Ouml;',
				'&times;',
				'&Oslash;',
				'&Ugrave;',
				'&Uacute;',
				'&Ucirc;',
				'&Uuml;',
				'&Yacute;',
				'&THORN;',
				'&szlig;',
				'&agrave;',
				'&aacute;',
				'&acirc;',
				'&atilde;',
				'&auml;',
				'&aring;',
				'&aelig;',
				'&ccedil;',
				'&egrave;',
				'&eacute;',
				'&ecirc;',
				'&euml;',
				'&igrave;',
				'&iacute;',
				'&icirc;',
				'&iuml;',
				'&eth;',
				'&ntilde;',
				'&ograve;',
				'&oacute;',
				'&ocirc;',
				'&otilde;',
				'&ouml;',
				'&divide;',
				'&oslash;',
				'&ugrave;',
				'&uacute;',
				'&ucirc;',
				'&uuml;',
				'&yacute;',
				'&thorn;',
				'&yuml;',
				'&fnof;',
				'&Alpha;',
				'&Beta;',
				'&Gamma;',
				'&Delta;',
				'&Epsilon;',
				'&Zeta;',
				'&Eta;',
				'&Theta;',
				'&Iota;',
				'&Kappa;',
				'&Lambda;',
				'&Mu;',
				'&Nu;',
				'&Xi;',
				'&Omicron;',
				'&Pi;',
				'&Rho;',
				'&Sigma;',
				'&Tau;',
				'&Upsilon;',
				'&Phi;',
				'&Chi;',
				'&Psi;',
				'&Omega;',
				'&alpha;',
				'&beta;',
				'&gamma;',
				'&delta;',
				'&epsilon;',
				'&zeta;',
				'&eta;',
				'&theta;',
				'&iota;',
				'&kappa;',
				'&lambda;',
				'&mu;',
				'&nu;',
				'&xi;',
				'&omicron;',
				'&pi;',
				'&rho;',
				'&sigmaf;',
				'&sigma;',
				'&tau;',
				'&upsilon;',
				'&phi;',
				'&chi;',
				'&psi;',
				'&omega;',
				'&thetasym;',
				'&upsih;',
				'&piv;',
				'&bull;',
				'&hellip;',
				'&prime;',
				'&Prime;',
				'&oline;',
				'&frasl;',
				'&weierp;',
				'&image;',
				'&real;',
				'&trade;',
				'&alefsym;',
				'&larr;',
				'&uarr;',
				'&rarr;',
				'&darr;',
				'&harr;',
				'&crarr;',
				'&lArr;',
				'&uArr;',
				'&rArr;',
				'&dArr;',
				'&hArr;',
				'&forall;',
				'&part;',
				'&exist;',
				'&empty;',
				'&nabla;',
				'&isin;',
				'&notin;',
				'&ni;',
				'&prod;',
				'&sum;',
				'&minus;',
				'&lowast;',
				'&radic;',
				'&prop;',
				'&infin;',
				'&ang;',
				'&and;',
				'&or;',
				'&cap;',
				'&cup;',
				'&int;',
				'&there4;',
				'&sim;',
				'&cong;',
				'&asymp;',
				'&ne;',
				'&equiv;',
				'&le;',
				'&ge;',
				'&sub;',
				'&sup;',
				'&nsub;',
				'&sube;',
				'&supe;',
				'&oplus;',
				'&otimes;',
				'&perp;',
				'&sdot;',
				'&lceil;',
				'&rceil;',
				'&lfloor;',
				'&rfloor;',
				'&lang;',
				'&rang;',
				'&loz;',
				'&spades;',
				'&clubs;',
				'&hearts;',
				'&diams;',
				'&quot;',
				'&amp;',
				'&lt;',
				'&gt;',
				'&OElig;',
				'&oelig;',
				'&Scaron;',
				'&scaron;',
				'&Yuml;',
				'&circ;',
				'&tilde;',
				'&ensp;',
				'&emsp;',
				'&thinsp;',
				'&zwnj;',
				'&zwj;',
				'&lrm;',
				'&rlm;',
				'&ndash;',
				'&mdash;',
				'&lsquo;',
				'&rsquo;',
				'&sbquo;',
				'&ldquo;',
				'&rdquo;',
				'&bdquo;',
				'&dagger;',
				'&Dagger;',
				'&permil;',
				'&lsaquo;',
				'&rsaquo;',
				'&euro;',
            ),
            array (
				'<ch:nbsp />',
				'<ch:iexcl />',
				'<ch:cent />',
				'<ch:pound />',
				'<ch:curren />',
				'<ch:yen />',
				'<ch:brvbar />',
				'<ch:sect />',
				'<ch:uml />',
				'<ch:copy />',
				'<ch:ordf />',
				'<ch:laquo />',
				'<ch:not />',
				'<ch:shy />',
				'<ch:reg />',
				'<ch:macr />',
				'<ch:deg />',
				'<ch:plusmn />',
				'<ch:sup2 />',
				'<ch:sup3 />',
				'<ch:acute />',
				'<ch:micro />',
				'<ch:para />',
				'<ch:middot />',
				'<ch:cedil />',
				'<ch:sup1 />',
				'<ch:ordm />',
				'<ch:raquo />',
				'<ch:frac14 />',
				'<ch:frac12 />',
				'<ch:frac34 />',
				'<ch:iquest />',
				'<ch:Agrave />',
				'<ch:Aacute />',
				'<ch:Acirc />',
				'<ch:Atilde />',
				'<ch:Auml />',
				'<ch:Aring />',
				'<ch:AElig />',
				'<ch:Ccedil />',
				'<ch:Egrave />',
				'<ch:Eacute />',
				'<ch:Ecirc />',
				'<ch:Euml />',
				'<ch:Igrave />',
				'<ch:Iacute />',
				'<ch:Icirc />',
				'<ch:Iuml />',
				'<ch:ETH />',
				'<ch:Ntilde />',
				'<ch:Ograve />',
				'<ch:Oacute />',
				'<ch:Ocirc />',
				'<ch:Otilde />',
				'<ch:Ouml />',
				'<ch:times />',
				'<ch:Oslash />',
				'<ch:Ugrave />',
				'<ch:Uacute />',
				'<ch:Ucirc />',
				'<ch:Uuml />',
				'<ch:Yacute />',
				'<ch:THORN />',
				'<ch:szlig />',
				'<ch:agrave />',
				'<ch:aacute />',
				'<ch:acirc />',
				'<ch:atilde />',
				'<ch:auml />',
				'<ch:aring />',
				'<ch:aelig />',
				'<ch:ccedil />',
				'<ch:egrave />',
				'<ch:eacute />',
				'<ch:ecirc />',
				'<ch:euml />',
				'<ch:igrave />',
				'<ch:iacute />',
				'<ch:icirc />',
				'<ch:iuml />',
				'<ch:eth />',
				'<ch:ntilde />',
				'<ch:ograve />',
				'<ch:oacute />',
				'<ch:ocirc />',
				'<ch:otilde />',
				'<ch:ouml />',
				'<ch:divide />',
				'<ch:oslash />',
				'<ch:ugrave />',
				'<ch:uacute />',
				'<ch:ucirc />',
				'<ch:uuml />',
				'<ch:yacute />',
				'<ch:thorn />',
				'<ch:yuml />',
				'<ch:fnof />',
				'<ch:Alpha />',
				'<ch:Beta />',
				'<ch:Gamma />',
				'<ch:Delta />',
				'<ch:Epsilon />',
				'<ch:Zeta />',
				'<ch:Eta />',
				'<ch:Theta />',
				'<ch:Iota />',
				'<ch:Kappa />',
				'<ch:Lambda />',
				'<ch:Mu />',
				'<ch:Nu />',
				'<ch:Xi />',
				'<ch:Omicron />',
				'<ch:Pi />',
				'<ch:Rho />',
				'<ch:Sigma />',
				'<ch:Tau />',
				'<ch:Upsilon />',
				'<ch:Phi />',
				'<ch:Chi />',
				'<ch:Psi />',
				'<ch:Omega />',
				'<ch:alpha />',
				'<ch:beta />',
				'<ch:gamma />',
				'<ch:delta />',
				'<ch:epsilon />',
				'<ch:zeta />',
				'<ch:eta />',
				'<ch:theta />',
				'<ch:iota />',
				'<ch:kappa />',
				'<ch:lambda />',
				'<ch:mu />',
				'<ch:nu />',
				'<ch:xi />',
				'<ch:omicron />',
				'<ch:pi />',
				'<ch:rho />',
				'<ch:sigmaf />',
				'<ch:sigma />',
				'<ch:tau />',
				'<ch:upsilon />',
				'<ch:phi />',
				'<ch:chi />',
				'<ch:psi />',
				'<ch:omega />',
				'<ch:thetasym />',
				'<ch:upsih />',
				'<ch:piv />',
				'<ch:bull />',
				'<ch:hellip />',
				'<ch:prime />',
				'<ch:Prime />',
				'<ch:oline />',
				'<ch:frasl />',
				'<ch:weierp />',
				'<ch:image />',
				'<ch:real />',
				'<ch:trade />',
				'<ch:alefsym />',
				'<ch:larr />',
				'<ch:uarr />',
				'<ch:rarr />',
				'<ch:darr />',
				'<ch:harr />',
				'<ch:crarr />',
				'<ch:lArr />',
				'<ch:uArr />',
				'<ch:rArr />',
				'<ch:dArr />',
				'<ch:hArr />',
				'<ch:forall />',
				'<ch:part />',
				'<ch:exist />',
				'<ch:empty />',
				'<ch:nabla />',
				'<ch:isin />',
				'<ch:notin />',
				'<ch:ni />',
				'<ch:prod />',
				'<ch:sum />',
				'<ch:minus />',
				'<ch:lowast />',
				'<ch:radic />',
				'<ch:prop />',
				'<ch:infin />',
				'<ch:ang />',
				'<ch:and />',
				'<ch:or />',
				'<ch:cap />',
				'<ch:cup />',
				'<ch:int />',
				'<ch:there4 />',
				'<ch:sim />',
				'<ch:cong />',
				'<ch:asymp />',
				'<ch:ne />',
				'<ch:equiv />',
				'<ch:le />',
				'<ch:ge />',
				'<ch:sub />',
				'<ch:sup />',
				'<ch:nsub />',
				'<ch:sube />',
				'<ch:supe />',
				'<ch:oplus />',
				'<ch:otimes />',
				'<ch:perp />',
				'<ch:sdot />',
				'<ch:lceil />',
				'<ch:rceil />',
				'<ch:lfloor />',
				'<ch:rfloor />',
				'<ch:lang />',
				'<ch:rang />',
				'<ch:loz />',
				'<ch:spades />',
				'<ch:clubs />',
				'<ch:hearts />',
				'<ch:diams />',
				'<ch:quot />',
				'<ch:amp />',
				'<ch:lt />',
				'<ch:gt />',
				'<ch:OElig />',
				'<ch:oelig />',
				'<ch:Scaron />',
				'<ch:scaron />',
				'<ch:Yuml />',
				'<ch:circ />',
				'<ch:tilde />',
				'<ch:ensp />',
				'<ch:emsp />',
				'<ch:thinsp />',
				'<ch:zwnj />',
				'<ch:zwj />',
				'<ch:lrm />',
				'<ch:rlm />',
				'<ch:ndash />',
				'<ch:mdash />',
				'<ch:lsquo />',
				'<ch:rsquo />',
				'<ch:sbquo />',
				'<ch:ldquo />',
				'<ch:rdquo />',
				'<ch:bdquo />',
				'<ch:dagger />',
				'<ch:Dagger />',
				'<ch:permil />',
				'<ch:lsaquo />',
				'<ch:rsaquo />',
				'<ch:euro />',
            ),
            $data
        );
    }



	// ------------------------------
	// ----- TAG HANDLERS BELOW -----
	// ------------------------------

	/**
	 * Default open and complete tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _default ($node) {
		$iul = $this->ignoreUntilLevel ();
		if ($iul && $iul < $node['level']) {
			return '';
		}
		$out = '<' . $node['tag'];

		if ($this->_addToHeader) {
			$this->toc[count ($this->toc) - 1]['value'] .= $node['value'];
		}

		if (isset ($node['attributes'])) {
			if (! $this->open) {
				if (isset ($node['attributes'][$this->prefix . 'replace'])) {
					$replace = $node['attributes'][$this->prefix . 'replace'];
					unset ($node['attributes'][$this->prefix . 'replace']);
					if ($node['type'] != 'complete') {
						$this->ignoreUntilLevel ($node['level']);
					}
					return $this->getVal ($replace, $node, 'path', true);
				} else {
					if (isset ($node['attributes'][$this->prefix . 'attributes'])) {
						$statements = $this->exp->splitStatement ($node['attributes'][$this->prefix . 'attributes']);
						foreach ($statements as $statement) {
							list ($attr, $expr) = $this->exp->splitAssignment ($statement);
							$node['attributes'][$attr] = $this->exp->evaluate ($expr, $node, 'path', true);
						}
						unset ($node['attributes'][$this->prefix . 'attributes']);
					} elseif (isset ($node['attributes'][$this->prefix . 'attrs'])) {
						$statements = $this->exp->splitStatement ($node['attributes'][$this->prefix . 'attrs']);
						foreach ($statements as $statement) {
							list ($attr, $expr) = $this->exp->splitAssignment ($statement);
							$node['attributes'][$attr] = $this->exp->evaluate ($expr, $node, 'path', true);
						}
						unset ($node['attributes'][$this->prefix . 'attrs']);
					}
					if (isset ($node['attributes'][$this->prefix . 'content'])) {
						$node['value'] = $this->getVal ($node['attributes'][$this->prefix . 'content'], $node, 'path', true);
						unset ($node['attributes'][$this->prefix . 'content']);
						if ($node['type'] != 'complete') {
							$this->ignoreUntilLevel ($node['level']);
						}
					}
					if (isset ($node['attributes'][$this->prefix . 'loop'])) {
						list ($varname, $expr) = $this->exp->splitAssignment ($node['attributes'][$this->prefix . 'loop']);
						$this->loop[] = array (
							'varname' => $varname,
							'expr' => $expr,
							'struct' => '',
						);
						$this->open = true;
						$this->open_var =& $this->loop[count ($this->loop) - 1]['struct'];
						$this->openUntilLevel = $node['level'];
						$this->isLoop = true;
						unset ($node['attributes'][$this->prefix . 'loop']);
					} elseif (isset ($node['attributes'][$this->prefix . 'repeat'])) {
						list ($varname, $expr) = $this->exp->splitAssignment ($node['attributes'][$this->prefix . 'repeat']);
						$this->loop[] = array (
							'varname' => $varname,
							'expr' => $expr,
							'struct' => '',
						);
						$this->open = true;
						$this->open_var =& $this->loop[count ($this->loop) - 1]['struct'];
						$this->openUntilLevel = $node['level'];
						$this->isLoop = true;
						unset ($node['attributes'][$this->prefix . 'repeat']);
					}
					if (isset ($node['attributes'][$this->prefix . 'condition'])) {
						list ($one, $two) = $this->exp->splitAssignment ($node['attributes'][$this->prefix . 'condition']);
						if ($one === 'not') {
							$negate = true;
							$expr = $two;
						} else {
							$negate = false;
							$expr = $node['attributes'][$this->prefix . 'condition'];
						}
						//echo '<pre>evaluating expression "' . "\t" . $expr . "\t" . '"</pre>';
						if ($negate && $this->exp->evaluate ($expr, $node, 'path', true)) {
							if ($node['type'] == 'complete') {
								return '';
							}
							$this->ignoreUntilLevel ($node['level']);
							return '';
						} elseif (! $negate && ! $this->exp->evaluate ($expr, $node, 'path', true)) {
							if ($node['type'] == 'complete') {
								return '';
							}
							$this->ignoreUntilLevel ($node['level']);
							return '';
						} else {
							unset ($node['attributes'][$this->prefix . 'condition']);
						}
					}
				}
			}
			foreach ($node['attributes'] as $key => $value) {
				if (strstr ($value, '${')) {
					$value = $this->exp->evaluate ('string: ' . $value, $node, 'path', true);
				}
				$out .= ' ' . $key . '="' . $value . '"';
			}
		}


		if ($node['type'] == 'complete') {
			if (isset ($node['value']) || ($this->isHtml && ! in_array ($node['tag'], $this->selfClosing))) {
				$out .= '>' . $node['value'] . '</' . $node['tag'] . '>';
			} else {
				$out .= ' />';
			}
		} else {
			$out .= '>';
			if (isset ($node['value'])) {
				$out .= $node['value'];
			}
		}
		return $out;
	}

	/**
	 * Default close tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _default_end ($node) {
		$out = '';
		$iul = $this->ignoreUntilLevel ();
		if ($iul && $iul < $node['level']) {
			return '';
		} elseif ($this->open && $node['level'] == $this->openUntilLevel && $this->isLoop) {
			// xt:loop ends here
			$this->open_var .= '</' . $node['tag'] . '>';

			$loop = array_shift ($this->loop);
			$this->open = false;
			unset ($this->open_var);
			$this->openUntilLevel = false;
			$this->isLoop = false;

			$list =& $this->exp->repeat ($loop['expr'], $node);
			$total = count ($list);
			foreach ($list as $key => $item) {
				$this->exp->setCurrent ($item, $loop['varname'], $key, $total);
				$out .= $this->fill ($this->wrap ($loop['struct']), $this->exp->getObject ('object'), true);
			}
			//$this->exp->setCurrent (new StdClass, $loop['varname'], 0, 0);

			return $out;
		} elseif ($iul && $node['level'] == $iul) {
			$this->ignoreUntilLevel (-1);
			return '';
		}
		return '</' . $node['tag'] . '>';
	}

	/**
	 * Default cdata node handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _default_cdata ($node) {
		$iul = $this->ignoreUntilLevel ();
		if ($iul && $iul < $node['level']) {
			return '';
		}

		if ($this->_addToHeader) {
			$this->toc[count ($this->toc) - 1]['value'] .= $node['value'];
		}

		return $node['value'];
	}

	/**
	 * Handler for xmlchar tags.  See
	 * http://xmlchar.sf.net/ for more information.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _ch_handler ($node) {
		$iul = $this->ignoreUntilLevel ();
		if ($iul && $iul < $node['level']) {
			return '';
		}

		if (preg_match ('/^ch:n[0-9]+$/', $node['tag'])) {
			$node['tag'] = str_replace ('n', '#', $node['tag']);
		}

		return '&' . str_replace ('ch:', '', $node['tag']) . ';';
	}

	/**
	 * Handler for header tags, if $makeToc is true.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _header_handler ($node) {
		$iul = $this->ignoreUntilLevel ();
		if ($iul && $iul < $node['level']) {
			return '';
		}

		$node['href'] = md5 ($node['tag'] . count ($this->toc) . $node['value']);
		$this->toc[] = $node;

		if ($node['type'] == 'complete') {
			$end = $node['value'] . '</' . $node['tag'] . '>';
		} else {
			$this->_addToHeader = true;
			$end = $node['value'];
		}

		$attrs = '';
		if (is_array ($node['attributes'])) {
			foreach ($node['attributes'] as $key => $value) {
				if (strstr ($value, '${')) {
					$value = $this->exp->evaluate ('string: ' . $value, $node, 'path', true);
				}
				$attrs .= ' ' . $key . '="' . $value . '"';
			}
		}

		return '<a name="' . $node['href'] . '" style="margin: 0px; padding: 0px; display: inline"></a><' . $node['tag'] . $attrs . '>' . $end;
	}

	/**
	 * Handler for end header tags, if $makeToc is true.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _header_end ($node) {
		$iul = $this->ignoreUntilLevel ();
		if ($iul && $iul < $node['level']) {
			return '';
		}

		$this->_addToHeader = false;
		return '</' . $node['tag'] . '>';
	}

	/**
	 * Generates a table of contents as an HTML unordered
	 * list, based on the $toc property.  Note: Also requires
	 * $buildToc to be set to true.
	 * 
	 * @access	public
	 * @param	string	$title
	 * @return	string
	 * 
	 */
	function makeToc ($title = '') {
		if (count ($this->toc) == 0 || ! $this->buildToc) {
			return '';
		}

		$out = '';
		if (! empty ($title)) {
			$out .= '<h2>' . $title . '</h2>' . NEWLINEx2;
		}

		$prev = '';
		$out .= '<ul>' . NEWLINE;
		$c = 0;
		foreach ($this->toc as $node) {
			if ($prev == $node['tag'] || empty ($prev)) {
				// same level
				$out .= TAB . '<li><a href="#' . $node['href'] . '">' . $node['value'] . '</a></li>' . NEWLINE;
			} elseif ($prev < $node['tag']) {
				// this tag under
				$c++;
				$out .= '<ul>' . NEWLINE;
				$out .= TAB . '<li><a href="#' . $node['href'] . '">' . $node['value'] . '</a></li>' . NEWLINE;
			} elseif ($prev > $node['tag']) {
				// close list and move down one
				$c--;
				$out .= '</ul>' . NEWLINE;
				$out .= TAB . '<li><a href="#' . $node['href'] . '">' . $node['value'] . '</a></li>' . NEWLINE;
			}
			$prev = $node['tag'];
		}
		while ($c > 0) {
			$out .= '</ul>' . NEWLINE;
			$c--;
		}
		return $out . '</ul>' . NEWLINEx2;
	}

	// <xt:tpl>

	/**
	 * Open tpl tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _tpl ($node) {
		return $node['value'];
	}

	// </xt:tpl>

	/**
	 * Close tpl tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _tpl_end ($node) {
		if (! isset ($node['value'])) {
			return '';
		}
		return $node['value'];
	}

	/**
	 * Cdata tpl tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _tpl_cdata ($node) {
		return $node['value'];
	}

	// <xt:doctype root="html" access="public" name="" uri="" />

	/**
	 * Creates a doctype declaration from an xt:doctype
	 * tag.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _doctype ($node) {
		$out = '<!DOCTYPE ' . $node['attributes']['root'];
		$out .= ' ' . strtoupper ($node['attributes']['access']) . ' "';
		if (isset ($node['attributes']['name'])) {
			$out .= $node['attributes']['name'] . '" "';
		}
		$out .= $node['attributes']['uri'] . "\">\n";
		return $out;
	}

	// <xt:xmldecl version="1.0" encoding="utf-8" />

	/**
	 * Creates an xml declaration tag from an xt:xmldecl
	 * tag.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _xmldecl ($node) {
		$out = '<?xml';
		foreach ($node['attributes'] as $key => $value) {
			$out .= ' ' . $key . '="' . $value . '"';
		}
		$out .= CLOSE_TAG;
		return $out;
	}

	// <xt:xmlstyle type="text/css" href="foo.css" />

	/**
	 * Creates an xml stylesheet declaration tag from an
	 * xt:xmlstyle tag.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _xmlstyle ($node) {
		$out = '<?xml-stylesheet';
		foreach ($node['attributes'] as $key => $value) {
			$out .= ' ' . $key . '="' . $value . '"';
		}
		$out .= CLOSE_TAG;
		return $out;
	}

	// <xt:comment>Message here</xt:comment>

	/**
	 * Creates an xml comment tag from an xt:comment
	 * tag.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _comment ($node) {
		if ($node['type'] == 'complete') {
			return '<!-- ' . $node['value'] . ' -->';
		}
		return '<!-- ' . $node['value'];
	}

	// <xt:comment>Message here</xt:comment>

	/**
	 * Creates an xml comment tag from an xt:comment
	 * tag.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _comment_end ($node) {
		return $node['value'] . ' -->';
	}

	// <xt:comment>Message here</xt:comment>

	/**
	 * Creates an xml comment tag from an xt:comment
	 * tag.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _comment_cdata ($node) {
		return $node['value'];
	}

	// <xt:note>Message here</xt:note> is an alias of <xt:comment></xt:comment>

	/**
	 * Creates an xml comment tag from an xt:note
	 * tag.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _note ($node) {
		return $this->_comment ($node);
	}

	/**
	 * Creates an xml comment tag from an xt:note
	 * tag.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _note_end ($node) {
		return $this->_comment_end ($node);
	}

	/**
	 * Creates an xml comment tag from an xt:note
	 * tag.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _note_cdata ($node) {
		return $this->_comment_cdata ($node);
	}

	// <xt:import pkg="saf.Misc.Alt" />
	// pkg is string

	/**
	 * Import tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _import ($node) {
		foreach (preg_split ('/, ?/', ltrim (rtrim ($node['attributes']['pkg']))) as $var) {
			global $loader;
			$loader->import ($var);
		}
		return '';
	}

	// <xt:set-obj name="c" new="Alt ('odd', 'even')" />
	// name is string, new is string

	/**
	 * Set-obj tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _set_obj ($node) {
		eval (CLOSE_TAG . OPEN_TAG . ' $this->exp->setObject (new ' . $node['attributes']['new'] . ', \'' . $node['attributes']['name'] . '\'); ' . CLOSE_TAG);
		return '';
	}

	// <xt:set name="varname" value="foobar" />
	// name is string, value is expression but string by default

	/**
	 * Set tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _set ($node) {
		if ($this->open) {
			return $this->_default ($node);
		}
		return $this->setVal ($node['attributes']['name'], $this->exp->evaluate ($node['attributes']['value'], $node, 'string', true));
	}

	// <xt:exec value="foobar" />
	// value is expression but php by default

	/**
	 * Exec tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _exec ($node) {
		$this->exp->evaluate ($node['attributes']['value'], $node, 'string', true);
		return '';
	}

	// <xt:register name="cgi" />
	// name is string

	/**
	 * Register tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _register ($node) {
		$this->exp->register ($node['attributes']['name']);
	}

	// <xt:inc name="other-template.tpl" />
	// all attributes are strings

	/**
	 * Open inc tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _inc ($node) {

		// evaluate the node name
		$node['attributes']['name'] = $this->exp->evaluate ($node['attributes']['name'], $node, 'string', true);

		if ($node['type'] != 'complete') {
			$this->ignoreUntilLevel ($node['level']);
		}

		if ($node['attributes']['type'] == 'csv') {
			if (! empty ($node['attributes']['delimiter'])) {
				$delimiters = array (
					'tab' => "\t",
					'comma' => ',',
					'colon' => ':',
					'pipe' => '|',
					'semicolon' => ';',
				);
				if (isset ($delimiters[$node['attributes']['delimiter']])) {
					$delimiter = $delimiters[$node['attributes']['delimiter']];
				} else {
					$delimiter = $node['attributes']['delimiter'];
				}
			} else {
				$delimiter = $delimiters['comma'];
			}

			$out = "<table>\n";
			$data = @file ($this->path () . '/' . $node['attributes']['name']);

			if (! is_array ($data)) {
				return '';
			}

			if ($node['attributes']['header'] == 'yes') {
				$headers = array_shift ($data);
				$out .= "\t<tr>\n";
				foreach (preg_split ('/' . $delimiter . '/', $headers) as $header) {
					$out .= "\t\t<th>" . $header . "</th>\n";
				}
				$out .= "\t</tr>\n";
			}

			foreach ($data as $line) {
				$out .= "\t<tr>\n";
				foreach (preg_split ('/' . $delimiter . '/', $line) as $item) {
					$out .= "\t\t<td>" . $item . "</td>\n";
				}
				$out .= "\t</tr>\n";
			}

			return $out . "<table>\n";

		} elseif ($node['attributes']['type'] == 'messy') {
			return $this->messy ($node['attributes']['name'], $this->exp->register['object']);

		} elseif ($node['attributes']['type'] == 'simple') {
			return template_simple ($node['attributes']['name'], $this->exp->register['object']);

		} elseif ($node['attributes']['type'] == 'virtual') {
			//ob_start ();
			if (strpos ($node['attributes']['name'], '/') === 0 || strpos ($node['attributes']['name'], '://') === false) {
				$url = site_url () . $node['attributes']['name'];
			} else {
				$url = $node['attributes']['name'];
			}
			//include ($url);
			//$o = ob_get_contents ();
			//ob_end_clean ();
			$o = @join ('', @file ($url));
			return $o;

		} elseif ($node['attributes']['type'] == 'xml') {
			$this->ignoreUntilLevel (-1);
			$this->open = true;
			$this->xmlinc = array (
				'node' => $node,
				'struct' => '',
			);
			$this->open_var =& $this->xmlinc['struct'];
			return '';

		} elseif ($node['attributes']['type'] == 'plain') {
			return @join ('', @file ($this->path () . '/' . $node['attributes']['name']));

		} else { // type is 'xt' or not specified
			$o = template_xt ($node['attributes']['name'], $this->exp->register['object']);
			if ($o === false) {
				return '<!-- ' . template_error () . ' (' . template_err_line () . ', ' . template_err_colnum () . ') -->';
			}
			return $o;
		}

	}

	/**
	 * Close inc tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _inc_end ($node) {
		if (! $this->xmlinc) {
			$this->ignoreUntilLevel (-1);
			return '';

		} else {

			$node = $this->xmlinc['node'];
			$struct = $this->xmlinc['struct'];
			$this->xmlinc = false;
			unset ($this->open_var);
			$this->open = false;

			if (! is_object ($this->sloppy)) {
				loader_import ('saf.XML.Sloppy');
				$this->sloppy = new SloppyDOM ();
			}
			if (! $doc = $this->sloppy->parseFromFile ($node['attributes']['name'])) {
				$this->error = $this->sloppy->error;
				$this->err_code = $this->sloppy->err_code;
				$this->err_line = $this->sloppy->err_line;
				$this->err_byte = $this->sloppy->err_byte;
				$this->err_colnum = $this->sloppy->err_colnum;
				return '';
			}
			if (! empty ($node['attributes']['item'])) {
				$items =& $doc->query ($node['attributes']['item']);
				if (! is_array ($items)) {
					$this->error = 'No nodes returned by the specified path';
					return '';
				}
			} else {
				$items =& $doc->root->children;
			}
			$res = '';

			foreach ($items as $item) {
				$this->exp->register['xml'] = $item->makeObj ();
				$res .= $this->fill ($this->wrap ($struct), $this->exp->getObject ('object'), true);
			}
			$this->exp->register['xml'] = false;
			return $res;

		}
	}

	// <xt:box name="syndicate" url="http://slashdot.org/slashdot.rdf" duration="1800" />
	// all attributes are strings

	/**
	 * Open box tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _box ($node) {
		if ($node['type'] != 'complete') {
			$this->ignoreUntilLevel ($node['level']);
		}
		foreach ($node['attributes'] as $key => $value) {
			if (strstr ($value, '${')) {
				$node['attributes'][$key] = $this->exp->evaluate ('string: ' . $value, $node, 'path', true);
			}
		}
		return $this->box ($this->exp->evaluate ($node['attributes']['name'], $node, 'string', true), $node['attributes']);
	}

	/**
	 * Close box tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _box_end ($node) {
		$this->ignoreUntilLevel (-1);
		return '';
	}












	// <xt:form name="signup" />

	/**
	 * Open form tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _form ($node) {
		if ($node['type'] != 'complete') {
			$this->ignoreUntilLevel ($node['level']);
		}
		foreach ($node['attributes'] as $key => $value) {
			if (strstr ($value, '${')) {
				$node['attributes'][$key] = $this->exp->evaluate ('string: ' . $value, $node, 'path', true);
			}
		}
		return $this->form ($this->exp->evaluate ($node['attributes']['name'], $node, 'string', true));
	}

	/**
	 * Close form tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _form_end ($node) {
		$this->ignoreUntilLevel (-1);
		return '';
	}








	// <xt:intl>Translated text here</xt:intl>
	// value is string

	/**
	 * Intl tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _intl ($node) {
		if ($this->open) {
			return $this->_default ($node);
		}
		global $intl;
		if (is_object ($intl)) {
			if (isset ($node['value'])) {
				$node['value'] = $this->reverseEntities ($node['value']);
			}
			if ($this->isHtml) {
				// add <span lang=""></span> tags around the value
				return $intl->get ($node['value'], $this->exp->register['object'], true);
			} else {
				return $intl->get ($node['value'], $this->exp->register['object']);
			}
		}
		return $node['value'];
	}

	/**
	 * Alias of _intl tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _translate ($node) {
		return $this->_intl ($node);
	}

	/**
	 * Alias of _intl tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _i18n ($node) {
		return $this->_intl ($node);
	}

	// <xt:val name="foo" />
	// name is expression

    // <xt:date format="format">My date here</xt:date>

	/**
	 * Date tag handler.
	 *
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 *
	 */
	function _date ($node) {
		if ($this->open) {
			return $this->_default ($node);
		}

		global $intl;
		if (is_object ($intl)) {
			$node['value'] = $this->reverseEntities ($node['value']);
			if (empty( $node['value'] )) {
				$node['value'] = date('r');
			}
			if ($this->isHtml) {
				// add <span lang=""></span> tags around the value
				return $intl->date ($node['attributes']['format'],
					$node['value'], $this->exp->register['object'], true);
			} else {
				return $intl->date ($node['attributes']['format'],
					$node['value'], $this->exp->register['object']);
			}
		}
		return $node['value'];
	}

	/**
	 * Var tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _var ($node) {
		if ($this->open) {
			return $this->_default ($node);
		}
		return $this->getVal ($node['attributes']['name'], $node, 'path', true);
	}

	// <xt:code><![CDATA[ echo 'hello<br />'; ]]></xt:code>
	// <xt:code highlighted="yes"><![CDATA[ echo 'hello<br />'; ]]></xt:code>
	// <xt:code language="asp"><![CDATA[ Response.Write ("hello<br />") ]]></xt:code>

	/**
	 * Code tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _code ($node) {
		if ($node['attributes']['highlighted'] == 'yes') {
			ob_start ();
			highlight_string ('<?php' . $node['value'] . '?' . '>');
			$out = ob_get_contents ();
			ob_end_clean ();
			return $out;
		}

		$languages = array (
			'php' => array ('<?php', '?' . '>'),
			'asp' => array ('<%', '%>'),
			'jsp' => array ('<%', '%>'),
			'javascript' => array ('<script language="javascript" type="text/javascript">', '</script>'),
			'runat_server' => array ('<script runat="server">', '</script>'),
			'asp_include' => array ('<%@', '%>'),
			'jsp_include' => array ('<%@', '%>'),
			'eperl' => array ('<:', ':>'),
			'eruby' => array ('<%', '%>'),
			'python' => array ('<%', '%>'),
		);

		if (! in_array ($node['attributes']['language'], array_keys ($languages))) {
			$node['attributes']['language'] = 'php';
		}
		$open = $languages[$node['attributes']['language']][0];
		$close = $languages[$node['attributes']['language']][1];

		return $open . $node['value'] . $close;
	}

	// <xt:transform directives="
	//		date, func, Date::format (${date})
	// " />
	// directives is a newline-separated list of strings

	/**
	 * Transform tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _transform ($node) {
		foreach (preg_split ("/;[\r\n\t]*/", $node['attributes']['directives'], -1, PREG_SPLIT_NO_EMPTY) as $var) {
			$var = ltrim (rtrim ($var));
			if (! empty ($var)) {
				list ($key, $type, $rule) = preg_split ('/, ?/', $var, 2);
				$rule = preg_replace ('/([a-zA-Z0-9_-]+)\./', '$\1->', $rule);
				$this->transformations[$key] = new TemplateTransformation ($type, $key, $rule);
			}
		}
		return '';
	}

	// <xt:sql query="select * from people">
	// query is an expression with default type string

	/**
	 * Open sql tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _sql ($node) {
		$this->sql[] = array (
			'query' => $this->exp->evaluate ($node['attributes']['query'], $node, 'string'),
			'totalquery' => $this->exp->evaluate ($node['attributes']['totalquery'], $node, 'string'),
			'bind' => array (),
			'sub' => '',
			'else' => '',
			'pager' => false,
			'limit' => 20,
			'offsetvar' => 'offset',
			'node' => $node,
		);
		if ($node['attributes']['pager'] == 'yes') {
			$this->sql[count ($this->sql) - 1]['pager'] = true;
			$this->sql[count ($this->sql) - 1]['limit'] = $node['attributes']['limit'];
			//$this->sql[count ($this->sql) - 1]['offsetvar'] = $node['attributes']['offsetvar'];
		}
		return '';
	}

	// </xt:sql>

	/**
	 * Close sql tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _sql_end ($node) {
		global $db, $cgi;

		$sql = array_shift ($this->sql);
		$out = '';

		if ($sql['pager']) {
			if (empty ($sql['totalquery'])) {
				$sql['totalquery'] = preg_replace ('/^select .+ from /i', 'select count(*) as total from ', $sql['query']);
			}
			$sql['totalquery'] = $this->exp->evaluate ($sql['totalquery'], $sql['node'], 'string', true);
			$tot = $db->fetch ($sql['totalquery'], $sql['bind']);
			if (! $tot) {
				$total = 0;
			} elseif (is_array ($tot)) {
				$total = $tot[0]->total;
			} else {
				$total = $tot->total;
			}
			$limit = $sql['limit'];
			if (! is_numeric ($limit)) {
				$limit = 20;
			}
			$offset = $cgi->offset;
			if (! is_numeric ($offset)) {
				$offset = 0;
			}

			$sql['query'] .= ' limit ' . $offset . ', ' . $limit;

			global $loader, $_SERVER;
			$loader->import ('saf.GUI.Pager');
// Start: SEMIAS #177 Pagination.
//	Not sure a fix is needed here.	
			$pager = new Pager ($offset, $limit, $total);
			$pager->setUrl ($_SERVER['SCRIPT_NAME'] . $cgi->makeQuery (array ($sql['offsetvar'], 'page', 'mode')));
			$pager->getInfo ();
			template_simple_register ('pager', $pager);
// END: SEMIAS
			$out .= template_simple ('<p>{spt PAGER_TEMPLATE_PREV_PAGE_LIST_NEXT}</p>', array ());
		}

		$sql['query'] = $this->exp->evaluate ($sql['query'], $sql['node'], 'string', true);
		$res = $db->fetch (
			$sql['query'],
			$sql['bind']
		);
		if (! $res) {
			//$this->error = $db->error;
			$this->exp->setObject ($db, 'result');
			return $this->fill ($this->wrap ($sql['else']), $sql['node'], true);
			$this->exp->setObject (false, 'result');
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		foreach ($res as $row) {
			$this->exp->setObject ($row, 'result');
			$out .= $this->fill ($this->wrap ($sql['sub']), $sql['node'], true);
		}
		$this->exp->unsetObject ('result');
		$this->rows = $db->rows;
		return $out;
	}

	// <xt:bind value="expr" />

	/**
	 * Bind tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _bind ($node) {
		$this->sql[count ($this->sql) - 1]['bind'][] = $this->exp->evaluate ($node['attributes']['value'], $node);
		return '';
	}

	// <xt:sub>

	/**
	 * Open sub tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _sub ($node) {
		if ($node['type'] != 'complete') {
			$this->open = true;
			$this->open_var =& $this->sql[count ($this->sql) - 1]['sub'];
			return $node['value'];
		} else {
			$this->open = true;
			$this->open_var =& $this->sql[count ($this->sql) - 1]['sub'];
			$this->_output ($node['value']);
			$this->open = false;
			unset ($this->open_var);
			return '';
		}
	}

	// </xt:sub>

	/**
	 * Close sub tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _sub_end ($node) {
		$this->open = false;
		unset ($this->open_var);
		return '';
	}

	// <xt:else>

	/**
	 * Open else tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _else ($node) {
		if ($this->open) {
			return $this->_default ($node);
		}
		if (count ($this->sql) > 0) {
			if ($node['type'] != 'complete') {
				$this->open = true;
				$this->open_var =& $this->sql[count ($this->sql) - 1]['else'];
				$this->openUntilLevel = $node['level'];
				return $node['value'];
			} else {
				//$this->open = true;
				//$this->open_var =& $this->sql[count ($this->sql) - 1]['else'];
				$this->open = false;
				unset ($this->open_var);
				return '';
			}
		} elseif (count ($this->if) > 0) {
			if ($node['type'] != 'complete') {
				$this->open = true;
				$this->open_var =& $this->if[count ($this->if) - 1]['else'];
				$this->openUntilLevel = $node['level'];
				return $node['value'];
			} else {
				//$this->open = true;
				//$this->open_var =& $this->if[count ($this->if) - 1]['else'];
				$this->open = false;
				unset ($this->open_var);
				return '';
			}
		}
	}

	// </xt:else>

	/**
	 * Close else tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _else_end ($node) {
		if ($this->open && $this->openUntilLevel !== false && $node['level'] == $this->openUntilLevel) {
			$this->open = false;
			unset ($this->open_var);
			return '';
		} else {
			return $this->_default_end ($node);
		}
	}

	// <xt:condition>

	/**
	 * Open condition tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _condition ($node) {
		if ($this->open) {
			return $this->_default ($node);
		}
		$this->if[] = array (
			'condition' => array (),
			'else' => false,
		);
		return '';
	}

	// </xt:condition>
	// must implement negation via a separate call to $this->exp->splitAssignment()

	/**
	 * Close condition tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _condition_end ($node) {
		if ($this->open) {
			return $this->_default_end ($node);
		}
		$if = array_shift ($this->if);
		//echo '<pre>';
		//print_r ($if);
		//echo '</pre>';
		foreach ($if['condition'] as $key => $condition) {
			$expr = $condition[0];
			$output = $condition[1];
			list ($one, $two) = $this->exp->splitAssignment ($expr);
			if ($one === 'not') {
				$negate = true;
				$expr = $two;
			} else {
				$negate = false;
			}
			//echo '<pre>evaluating expression "' . "\t" . $expr . "\t" . '"</pre>';
			if ($negate && ! $this->exp->evaluate ($expr, $node, 'path', true)) {
				return $this->fill ($this->wrap ($output), $this->exp->register['object'], true);
			} elseif (! $negate && $this->exp->evaluate ($expr, $node, 'path', true)) {
				return $this->fill ($this->wrap ($output), $this->exp->register['object'], true);
			}
		}
		if (! empty ($if['else'])) {
			return $this->fill ($this->wrap ($if['else']), $this->exp->register['object'], true);
		}
		return '';
	}

	// <xt:if expr="foo/bar">
	// expr is an expression

	/**
	 * Open if tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _if ($node) {
		if ($this->open) {
			return $this->_default ($node);
		}
		$this->open = true;
		$this->if[count ($this->if) - 1]['condition'][] = array ($node['attributes']['expr'], $node['value']);
		$this->open_var =& $this->if[count ($this->if) - 1]['condition'][count ($this->if[count ($this->if) - 1]['condition']) - 1][1];
		$this->openUntilLevel = $node['level'];
		return '';
	}

	// </xt:if>

	/**
	 * Close if tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _if_end ($node) {
		if ($this->open && $this->openUntilLevel !== false && $node['level'] == $this->openUntilLevel) {
			$this->open = false;
			unset ($this->open_var);
			$this->openUntilLevel = false;
			return '';
		} else {
			return $this->_default_end ($node);
		}
	}

	// <xt:elseif expr="foo/bar">
	// expr is an expression

	/**
	 * Open elseif tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _elseif ($node) {
		if ($this->open) {
			return $this->_default ($node);
		}
		$this->open = true;
		$this->if[count ($this->if) - 1]['condition'][] = array ($node['attributes']['expr'], $node['value']);
		$this->open_var =& $this->if[count ($this->if) - 1]['condition'][count ($this->if[count ($this->if) - 1]['condition']) - 1][1];
		$this->openUntilLevel = $node['level'];
		return '';
	}

	// </xt:if>

	/**
	 * Close elseif tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _elseif_end ($node) {
		if ($this->open && $this->openUntilLevel !== false && $node['level'] == $this->openUntilLevel) {
			$this->open = false;
			unset ($this->open_var);
			$this->openUntilLevel = false;
			return '';
		} else {
			return $this->_default_end ($node);
		}
	}

	/**
	 * Open elsif tag handler.  Alias of _elseif.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _elsif ($node) {
		return $this->_elseif ($node);
	}

	/**
	 * Close elsif tag handler.  Alias of _elseif_end.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _elsif_end ($node) {
		return $this->_elseif_end ($node);
	}

	// <xt:loop through="item foo/list">
	// through is an expression

	/**
	 * Open loop tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _loop ($node) {
		if ($this->open) {
			return $this->_default ($node);
		}
		list ($varname, $expr) = $this->exp->splitAssignment ($node['attributes']['through']);
		$this->loop[] = array (
			'varname' => $varname,
			'expr' => $expr,
			'struct' => $node['value'],
		);
		$this->open = true;
		$this->open_var =& $this->loop[count ($this->loop) - 1]['struct'];
		$this->openUntilLevel = $node['level'];
		return '';
	}

	// </xt:loop>

	/**
	 * Close loop tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _loop_end ($node) {
		if ($this->open && $this->openUntilLevel !== false && $node['level'] == $this->openUntilLevel) {
			$this->open = false;
			unset ($this->open_var);
			$this->openUntilLevel = false;
			$loop = array_pop ($this->loop);
			$res = '';
/*
			ob_start ();
			echo '<pre>';
			print_r ($loop);
			print_r ($this->exp->register['loop']);
			echo '</pre>';
			$res .= htmlentities (ob_get_contents ());
			ob_end_clean ();
//			echo '<pre>' . $res . '</pre>';
			$res = '<pre>' . $res . '</pre>';

//			$res = '';
*/
			$list =& $this->exp->repeat ($loop['expr'], $node);
			$total = count ($list);
			foreach ($list as $key => $item) {
				$this->exp->setCurrent ($item, $loop['varname'], $key, $total);
				$res .= $this->fill ($this->wrap ($loop['struct']), $this->exp->getObject ('object'), true);
			}
			//$this->exp->setCurrent (new StdClass, $loop['varname'], 0, 0);
			return $res;
		} else {
			return $this->_default_end ($node);
		}
	}

	// <xt:block name="foo">
	// name is regular string

	/**
	 * Open block tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _block ($node) {
		if (! isset ($this->block[$node['attributes']['name']])) {
			$this->block[$node['attributes']['name']] = '';
		}
		$this->open = true;
		$this->open_var =& $this->block[$node['attributes']['name']];

		$this->_output ($node['value']);

		if ($node['type'] == 'complete') {
			return $this->_block_end ($node);
		}
		return '';
	}

	// </xt:block>

	/**
	 * Close block tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _block_end ($node) {
		$this->open = false;
		unset ($this->open_var);
		return '';
	}

	// <xt:show block="blockname" paramOne="foo" secondParam="bar" />
	// block is regular string, additional params pertain to block
	// vars, ie. block/firstname, block/lastname

	/**
	 * Open show tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _show ($node) {
		if ($node['type'] != 'complete') {
			$this->ignoreUntilLevel ($node['level']);
		}
		$this->exp->register['block'] = $node['attributes'];
		return $this->fill ($this->wrap ($this->block[$node['attributes']['block']]), $this->exp->getObject (), true);
	}

	/**
	 * Close show tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _show_end ($node) {
		$this->ignoreUntilLevel (-1);
		return '';
	}

	/**
	 * Open cache tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _cache ($node) {
		$user = '';

		if ($node['attributes']['scope'] == 'session') {
			if (session_valid ()) {
				$user = session_username ();
				$cacheable = true;
			} else {
				$cacheable = false;
			}
		} else {
			$cacheable = true;
		}

		$this->cache = false;

		if (! $cacheable) {
			return '';
		}

		if (! isset ($node['attributes']['scope'])) {
			$node['attributes']['scope'] = 'application';
		}

		if (isset ($node['attributes']['duration'])) {
			$duration = (int) $node['attributes']['duration'];
		} else {
			$duration = $this->cacheDuration;
		}

		if (! isset ($node['attributes']['id'])) {
			$this->cacheCount++;
			$node['attributes']['id'] = $this->cacheCount;
		}

		loader_import ('saf.Cache');
		$this->_cache = new Cache ($this->cacheLocation . $node['attributes']['scope']);

		if ($this->_cache->expired ($this->file . ':' . $node['attributes']['id'] . ':' . $user, $duration)) {
			// re-cache
			$this->cache = $this->file . ':' . $node['attributes']['id'] . ':' . $user;
			$this->output2 = $this->output;
			$this->output = '';
			return '';

		} else {
			// show from cache
			$out = $this->_cache->show ($this->file . ':' . $node['attributes']['id'] . ':' . $user);
			$this->ignoreUntilLevel ($node['level']);
		}

		return $out;
	}

	/**
	 * Close cache tag handler.
	 * 
	 * @access	private
	 * @param	associative array	$node
	 * @return	string
	 * 
	 */
	function _cache_end ($node) {
		if ($this->ignoreUntilLevel () == $node['level']) {
			$this->ignoreUntilLevel (-1);
		}

		if (! $this->cache) {
			return '';
		}

		$this->_cache->file ($this->cache, $this->output);

		$out = $this->output;

		$this->cache = false;
		$this->_cache = false;

		$this->output = $this->output2;
		$this->output2 = '';

		return $out;
	}

/*
	function _datagrid ($node) {
		$this->grid[] = new StdClass;
		foreach ($node['attributes'] as $attr => $value) {
			$this->grid[count ($this->grid) - 1]->{$attr} = $value;
		}
		$this->grid[count ($this->grid) - 1]->columns = array ();
		return '';
	}

	function _column ($node) {
		$col = new StdClass;
		foreach ($node['attributes'] as $attr => $value) {
			$col->{$attr} = $value;
		}
		$col->rules = array ();
		$this->grid[count ($this->grid) - 1]->columns[] = $col;
		return '';
	}

	function _rule ($node) {
		return '';
	}

	function _datagrid_end ($node) {
		return '';
	}
*/

	
}



function template_xt ($tpl, $obj = '', $carry = false) {
	return $GLOBALS['tpl']->fill ($tpl, $obj, $carry);
}

function template_messy ($tpl, $obj = '', $carry = false) {
	return $GLOBALS['tpl']->messy ($tpl, $obj, $carry);
}

function template_validate ($data) {
	return $GLOBALS['tpl']->validate ($data);
}

function template_wrap ($data) {
	return $GLOBALS['tpl']->wrap ($data);
}

function template_error () {
    return $GLOBALS['tpl']->error;
}

function template_err_line () {
    return $GLOBALS['tpl']->err_line;
}

function template_err_colnum () {
    return $GLOBALS['tpl']->err_colnum;
}

function template_convert_entities ($data) {
    return $GLOBALS['tpl']->convertEntities ($data);
}

function template_toc ($title = '') {
	return $GLOBALS['tpl']->makeToc ($title);
}

function template_bind ($path, $data) {
	return $GLOBALS['tpl']->bind ($path, $data);
}

function template_bind_attr ($path, $attr, $value) {
	return $GLOBALS['tpl']->bindAttr ($path, $attr, $value);
}

function template_parse_body ($body) {
	// boxes and forms
	preg_match_all ('/<xt:(box|form)([^>]+)>.*<\/xt:(box|form)>/m', $body, $matches, PREG_SET_ORDER);
	foreach ($matches as $match) {
		preg_match_all ('/([a-zA-Z0-9_]+)="([^"]*)"/m', $match[2], $attrs, PREG_SET_ORDER);
		$box = array ();
		foreach ($attrs as $attr) {
			$box[$attr[1]] = $attr[2];
		}
		if ($match[1] == 'box') {
			$body = str_replace ($match[0], loader_box ($box['name'], $box), $body);
		} elseif ($match[1] == 'form') {
			$body = str_replace ($match[0], loader_form ($box['name'], $box), $body);
		}
	}

	// toc
	preg_match_all ('/<(h[1-6]).*>(.*)<\/h[1-6]>/m', $body, $matches, PREG_SET_ORDER);
	$toc = array ();
	foreach ($matches as $match) {
		$href = md5 ($match[1] /*. count ($toc)*/ . $match[2]);
		$toc[] = array (
			'tag' => $match[1],
			'href' => $href,
			'value' => $match[2],
		);
		$body = str_replace ($match[0], '<a name="' . $href . '" style="margin: 0px; padding: 0px; display: inline"></a>' . $match[0], $body);
	}

	if (count ($toc) == 0) {
		$GLOBALS['page']->toc = '';
	} else {
		$prev = '';
		$out = '<ul>' . NEWLINE;
		$c = 0;
		foreach ($toc as $node) {
			if ($prev == $node['tag'] || empty ($prev)) {
				// same level
				$out .= TAB . '<li><a href="#' . $node['href'] . '">' . $node['value'] . '</a></li>' . NEWLINE;
			} elseif ($prev < $node['tag']) {
				// this tag under
				$c++;
				$out .= '<ul>' . NEWLINE;
				$out .= TAB . '<li><a href="#' . $node['href'] . '">' . $node['value'] . '</a></li>' . NEWLINE;
			} elseif ($prev > $node['tag']) {
				// close list and move down one
				$c--;
				$out .= '</ul>' . NEWLINE;
				$out .= TAB . '<li><a href="#' . $node['href'] . '">' . $node['value'] . '</a></li>' . NEWLINE;
			}
			$prev = $node['tag'];
		}
		while ($c > 0) {
			$out .= '</ul>' . NEWLINE;
			$c--;
		}
		$GLOBALS['page']->toc = $out . '</ul>' . NEWLINEx2;
	}

	return $body;
}

?>