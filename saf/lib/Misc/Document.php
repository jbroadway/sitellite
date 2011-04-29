<?php

/**
 * This package contains the data of the current page request, and renders it for the
 * Sitellite Content Server.  It also provides several global-level functions that
 * provide developers with control over the contents and the properties of the page
 * request.
 *
 * @package Misc
 */
class Document {
	/**
	 * List of HTTP headers to be sent with the document.  @see addHeader()
	 */
	var $headers = array ();

	/**
	 * List of metadata attributes, which can be rendered as HTML meta tags using
	 * the makeMeta() method.  To render them into your XT templates, simply
	 * add an xt:var tag with the name "makeMeta".
	 */
	var $meta = array ();

	/**
	 * List of javascript scripts, which can be rendered as HTML script tags using
	 * the makeJavascript() method.  To render them into your XT templates, simply
	 * add an xt:var tag with the name "makeJavascript".
	 */
	var $scripts = array ();

	/**
	 * Array used to prevent duplicate loading of stylesheets.
	 */
	var $styles = array ();

	/**
	 * The template set to use for output.
	 */
	var $template_set = false;

	/**
	 * Whether compile() is underway or not.
	 */
	var $compiling = false;

	/**
	 * Extra elements to load in the <head> after the page is rendered.
	 */
	var $extra_head_elements = array ();

	/**
	 * Constructor method.  If $data is passed to the constructor, it will pass the
	 * $data on to the set() method.
	 *
	 * @param array hash
	 */
	function Document ($data = array ()) {
		$this->error = false;
		$this->headers = array ();
		if (conf ('Server', 'send_version_header')) {
			$this->addMeta ('generator', 'Sitellite Content Server ' . SITELLITE_VERSION);
		}
		$this->set ($data);
	}

	/**
	 * Sets properties of the current Document object, such as title, id, body, etc.
	 *
	 * @param array hash
	 */
	function set ($data = array ()) {
		if (is_object ($data)) {
			foreach (get_object_vars ($data) as $key => $value) {
				$this->{$key} = $value;
			}
		} elseif (is_array ($data)) {
			foreach ($data as $key => $value) {
				$this->{$key} = $value;
			}
		} else {
			$this->body = $data;
		}
	}

	/**
	 * Adds an HTTP header to send when compile() is called.
	 *
	 * @param string
	 */
	function addHeader ($header) {
		$this->headers[] = $header;
	}

	/**
	 * Sends all HTTP headers associated with the current document.
	 * Called by compile() automatically.
	 */
	function sendHeaders () {
		foreach ($this->headers as $header) {
			header ($header);
		}
	}

	/**
	 * Adds a metadata property to the global template.
	 *
	 * @param string
	 * @param string
	 */
	function addMeta ($key, $value, $name = 'name') {
		if ($this->compiling) {
			$this->extra_head_elements[] = '<meta ' . $name . '="' . $key . '" content="' . str_replace ('"', '&quot;', $value) . '" />' . NEWLINE;
		} else {
			template_bind (
				'/html/head',
				'<meta ' . $name . '="' . $key . '" content="' . str_replace ('"', '&quot;', $value) . '" />' . NEWLINE
			);
		}
	}

	/**
	 * Note: Deprecated in favour of XT's automatic tag binding capabilities.
	 * Compiles the metadata properties of the current document into HTML meta tags,
	 * which are returned as one big XHTML-compliant string.
	 *
	 * @return string
	 */
	function makeMeta () {
		return '';
	}

	/**
	 * Adds a script to the global template.
	 *
	 * @param string
	 */
	function addScript ($script) {
		if (strpos ($script, '<script') !== 0) {
			$new = '<script language="javascript" type="text/javascript"';
			if (strstr ($script, NEWLINE)) {
				$new .= '><!--' . NEWLINEx2 . $script . NEWLINEx2 . '// --></script>' . NEWLINE;
			} else {
				$new .= ' src="' . $script . '"></script>' . NEWLINE;
			}
			$script = $new;
		}
		if (! in_array ($script, $this->scripts)) {
			if ($this->compiling) {
				$this->extra_head_elements[] = $script;
			} else {
				template_bind (
					'/html/head',
					$script
				);
			}
			$this->scripts[] = $script;
		}
	}

	/**
	 * Adds a stylesheet to the global template.
	 *
	 * @param string
	 */
	function addStyle ($style) {
		if (strpos ($style, '<style') !== 0) {
			if (strstr ($style, NEWLINE)) {
				$new = '<style type="text/css">' . NEWLINEx2 . $style . NEWLINEx2 . '</style>' . NEWLINE;
			} else {
				$new = '<link rel="stylesheet" type="text/css" href="' . $style . '" />' . NEWLINE;
			}
			$style = $new;
		}
		if (! in_array ($style, $this->styles)) {
			if ($this->compiling) {
				$this->extra_head_elements[] = $style;
			} else {
				template_bind (
					'/html/head',
					$style
				);
			}
			$this->styles[] = $style;
		}
	}

	/**
	 * Note: Deprecated in favour of XT's automatic tag binding capabilities.
	 * Compiles the javascript of the current document into HTML script tags,
	 * which are returned as one big XHTML-compliant string.
	 *
	 * @return string
	 */
	function makeJavascript () {
		return '';
	}

	/**
	 * Adds a link tag to the global template.
	 *
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	function addLink ($rel, $type, $href, $charset = false, $hreflang = false, $name = false) {
		$link = '<link rel="' . $rel . '" type="' . $type . '" href="' . $href . '"';
		if ($charset) {
			$link .= ' charset="' . $charset . '"';
		}
		if ($hreflang) {
			$link .= ' hreflang="' . $hreflang . '"';
		}
		if ($name) {
			$link .= ' title="' . $name . '"';
		}
		$link .= ' />' . NEWLINE;
		if ($this->compiling) {
			$this->extra_head_elements[] = $link;
		} else {
			template_bind (
				'/html/head',
				$link
			);
		}
	}

	/**
	 * "Compiles" the document.  This includes sending any headers, checking
	 * isExternal(), then calling useTemplate() to render the document to the
	 * appropriate template.
	 *
	 * @return string
	 */
	function compile () {
		$this->compiling = true;
		$this->extra_head_elements = array ();

		if (isset ($this->onload)) {
			template_bind_attr (
				'/html/body',
				'onload',
				$this->onload
			);
		}
		if (isset ($this->onunload)) {
			template_bind_attr (
				'/html/body',
				'onunload',
				$this->onunload
			);
		}
		if (isset ($this->onfocus)) {
			template_bind_attr (
				'/html/body',
				'onfocus',
				$this->onfocus
			);
		}
		if (isset ($this->onblur)) {
			template_bind_attr (
				'/html/body',
				'onblur',
				$this->onblur
			);
		}
		if (isset ($this->onclick)) {
			template_bind_attr (
				'/html/body',
				'onclick',
				$this->onclick
			);
		}
		$this->sendHeaders ();
		$this->isExternal ();
		$res = $this->useTemplate ();

		$this->compiling = false;
		foreach ($this->extra_head_elements as $element) {
			$res = str_replace ('</head>', TAB . $element . NEWLINE . '</head>', $res);
		}

		return $res;
	}

	/**
	 * Checks for an $external property of the document object, which if found
	 * is understood to represent an external document that this object is
	 * actually an alias of, and so it will forward the request on to that
	 * document.
	 */
	function isExternal () {
		if (! empty ($this->external)) {
			global $intl;
			if ($intl->negotiation == 'url') {
				$intl_prefix = '/' . $intl->language;
			} else {
				$intl_prefix = '';
			}
			if (conf ('Site', 'remove_index')) {
				$index = '/';
			} else {
				$index = '/index/';
			}
			if (session_admin ()) {
				if (! preg_match ('|^[a-zA-Z0-9]+://|', $this->external)) {
					if (strpos ($this->external, '/') === 0) {
						if (site_secure () && cgi_is_https ()) {
							$ext = 'https://' . site_domain () . $this->external;
						} else {
							$ext = 'http://' . site_domain () . $this->external;
						}
					} else {
						if (site_secure () && cgi_is_https ()) {
							$ext = 'https://' . site_domain () . site_prefix () . $intl_prefix . $index . $this->external;
						} else {
							$ext = 'http://' . site_domain () . site_prefix () . $intl_prefix . $index . $this->external;
						}
					}
				} else {
					$ext = $this->external;
				}
				$this->body = '<p>' . intl_get ('This page is a placeholder for the following external resource') . ':</p><p><a href="' . $ext . '">' . $ext . '</a></p>';
				return false;
			}
			if (! preg_match ('|^[a-zA-Z0-9]+://|', $this->external)) {
				if (conf ('Site', 'remove_index')) {
					$this->external = str_replace ('/index/', '/', $this->external);
				}
				if (strpos ($this->external, '/') === 0) {
					if (site_secure () && cgi_is_https ()) {
						header ('Location: https://' . site_domain () . $this->external);
					} else {
						header ('Location: http://' . site_domain () . $this->external);
					}
				} else {
					if (site_secure () && cgi_is_https ()) {
						header ('Location: https://' . site_domain () . site_prefix () . $intl_prefix . $index . $this->external);
					} else {
						header ('Location: http://' . site_domain () . site_prefix () . $intl_prefix . $index . $this->external);
					}
				}
			} else {
				header ('Location: ' . $this->external);
			}
			exit;
		}
	}

	/**
	 * Determines which template to use to render the document for output
	 * to the visitor.  The template is chosen based on the following
	 * conditions:
	 *
	 * - Templates all live in inc/html in individual template set directories
	 *   (ie. themes).  The template set can be specified with page_template_set(),
	 *   or via the default_template_set configuration option.
	 * - The $cgi->mode value, which defaults to "html" if unspecified
	 * - The $template property of this object is appended to the mode like
	 *   this: html.index.tpl where html is the mode and index is the $template
	 *   value.  The template can be specified with the page_template() function,
	 *   or via the default_template configuration option.
	 * - If that document is not found, we then check for such a document
	 *   based on the names of parent documents in the hierarchy, but only
	 *   those that are designated as section roots.  Each of these is then
	 *   used as a replacement for the $template property.
	 * - If none is found, then the default html.{default_template}.tpl is used,
	 *   where html is the mode.
	 *
	 * Note that the templates are always in XT format.  For info on the XT template
	 * language, see the class documentation under XML/XT at sitellite.org/docs
	 *
	 * @return string
	 */
	function useTemplate () {
		global $tpl, $cgi, $menu, $conf;

		if ($this->template_set) {
			$set = $this->template_set;
		} else {
			$set = $conf['Server']['default_template_set'];
		}

		$old_path = $tpl->path;

		$tpl->path = $tpl->path . '/' . $set;

		if (@file_exists ($tpl->path . '/modes.php')) {
			$modes = ini_parse ($tpl->path . '/modes.php');
			if (isset ($modes[$cgi->mode]['content_type'])) {
				global $intl;
				if (! empty ($intl->charset)) {
					header ('Content-Type: ' . $modes[$cgi->mode]['content_type'] . '; charset=' . $intl->charset);
				} else {
					header ('Content-Type: ' . $modes[$cgi->mode]['content_type']);
				}
			}

			if (is_array ($modes[$cgi->mode])) {
				foreach ($modes[$cgi->mode] as $k => $v) {
					if (strpos ($k, 'filter ') === 0) {
						list ($field, $filter) = preg_split ('/: ?/', $v);
						if ($field == 'final') {
							$final = false;
							if (strstr ($filter, '.')) {
								// it's a package, needs inclusion first
								$filter_name = strtolower (str_replace ('.', '_', $filter)) . '_content_filter';
								if (loader_import ($filter)) {
									if (function_exists ($filter_name)) {
										$final = $filter_name;
									}
								}
							} else {
								// it's an ordinary function
								$final = $filter;
							}
						} else {
							if (strstr ($filter, '.')) {
								// it's a package, needs inclusion first
								$filter_name = strtolower (str_replace ('.', '_', $filter)) . '_content_filter';
								if (loader_import ($filter)) {
									if (function_exists ($filter_name)) {
										$this->{$field} = $filter_name ($this->{$field});
									}
								}
							} else {
								// it's an ordinary function
								$this->{$field} = $filter ($this->{$field});
							}
						}
					}
				}
			}
		} else {
			$modes = array ('html' => array ('content_type' => 'text/html'));
		}

		if ($cgi->mode == 'html') {
			foreach ($modes as $k => $m) {
				if ($k != 'html') {
					$alt_url = $_SERVER['REQUEST_URI'];
					if (strpos ($alt_url, '?') !== false) {
						$alt_url = str_replace ('?', '/mode.' . $k . '?', $alt_url);
					} else {
						$alt_url .= '/mode.' . $k;
					}
					page_add_link ('alternate', $m['content_type'], $alt_url);
				}
			}
		}

		if (! empty ($this->template) && @file_exists (getcwd () . '/' . $tpl->path . '/' . $cgi->mode . '.' . $this->template . '.tpl')) {
			$response = $tpl->fill ($cgi->mode . '.' . $this->template . '.tpl', $this);
			$_t = $cgi->mode . '.' . $this->template . '.tpl';
		} elseif (! empty ($this->below_page)) {
			// inherit section template
			$useTemplate = false;
			$parent = $this->below_page;
			while ($parent) {
				$pp = db_single ('SELECT below_page, template FROM sitellite_page WHERE id=?', $parent);
				if ($pp->template) {
					$useTemplate = $pp->template;
					break;
				}
				$parent = $pp->below_page;
			}
			if ($useTemplate) {
				$response = $tpl->fill ($cgi->mode . '.' . $useTemplate . '.tpl', $this);
				$_t = $cgi->mode . '_' . $useTemplate . '.tpl';
			} elseif (! empty ($conf['Server']['default_template']) && @file_exists (getcwd () . '/' . $tpl->path . '/' . $cgi->mode . '.' . $conf['Server']['default_template'] . '.tpl')) {
				$response = $tpl->fill ($cgi->mode . '.' . $conf['Server']['default_template'] . '.tpl', $this);
				$_t = $cgi->mode . '.' . $conf['Server']['default_template'] . '.tpl';
			} elseif (@file_exists (getcwd () . '/' . $tpl->path . '/' . $cgi->mode . '.default.tpl')) {
				$response = $tpl->fill ($cgi->mode . '.default.tpl', $this);
				$_t = $cgi->mode . '.default.tpl';
			} else {
				$cgi->mode = 'html';
				$response = $tpl->fill ($cgi->mode . '.default.tpl', $this);
				$_t = $cgi->mode . '.default.tpl';
			}

		} elseif (! empty ($conf['Server']['default_template']) && @file_exists (getcwd () . '/' . $tpl->path . '/' . $cgi->mode . '.' . $conf['Server']['default_template'] . '.tpl')) {
			$response = $tpl->fill ($cgi->mode . '.' . $conf['Server']['default_template'] . '.tpl', $this);
			$_t = $cgi->mode . '.' . $conf['Server']['default_template'] . '.tpl';
		} elseif (@file_exists (getcwd () . '/' . $tpl->path . '/' . $cgi->mode . '.default.tpl')) {
			$response = $tpl->fill ($cgi->mode . '.default.tpl', $this);
			$_t = $cgi->mode . '.default.tpl';
		} else {
			$cgi->mode = 'html';
			$response = $tpl->fill ($cgi->mode . '.default.tpl', $this);
			$_t = $cgi->mode . '.default.tpl';
		}
		if ($response === false) {
			$response = '<p>' . $tpl->error . ' (Template: ' . $_t . ', Line ' . $tpl->err_line . ', Column ' . $tpl->err_colnum . ')</p>';
		}

		if ($final) {
			$response = $final ($response);
		}

		$tpl->path = $old_path;

		return $response;
	}

	/**
	 * Returns the section name of the current page, or false if the page is
	 * not in any section.  A section is a page that has its 'is_section'
	 * property set to true in $menu, and is set under the 'Properties' tab
	 * of the Sitellite web page editor in the field named 'Is This a Section
	 * Index?'.  Section pages differ from ordinary pages in that their
	 * template setting (also under the 'Properties' tab) will inherit to
	 * all child pages, unless those pages explicitly declare their own
	 * template.
	 *
	 * @return string
	 */
	function getSection () {
		loader_box ('sitellite/nav/init');
		global $menu;
		if ($menu->{'items_' . $this->id}->is_section) {
			return $this->id;
		}
		$parent = $this->below_page;
		while (true) {
			if ($menu->{'items_' . $parent}->is_section) {
				return $parent;
			} elseif (is_object ($menu->{'items_' . $parent}->parent)) {
				$parent = $menu->{'items_' . $parent}->parent->id;
			} else {
				break;
			}
		}
		return false;
	}

	/**
	 * Gets the title of the current or the specified page.
	 *
	 * @param string
	 * @return string
	 */
	function getTitle ($page = false) {
		if (! $page) {
			return $this->title;
		} else {
			global $menu;
			return $menu->{'items_' . $page}->title;
		}
	}

    /**
	 * Gets the title of the current or the specified page.
	 *
	 * @param string
	 * @return string
	 */
	function getPageId ($page = false) {
		if (! $page) {
   			return $this->id;
		} else {
			global $menu;
			return $menu->{'items_' . $page}->id;
		}
	}

    /**
	 * Gets the title of the current or the specified page.
	 *
	 * @param string
	 * @return string
	 */
	function getNavTitle ($page = false) {
		if (! $page) {
   			return $this->nav_title;
		} else {
			global $menu;
			return $menu->{'items_' . $page}->nav_title;
		}
	}

	/**
	 * Determines whether the specified page ID is a parent of the current
	 * page.
	 *
	 * @param string
	 * @return boolean
	 */
	function isParent ($parent) {
		global $menu;
		if (! $this->id || ! is_object ($menu->{'items_' . $this->id})) {
			return false;
		}

		$item =& $menu->{'items_' . $this->id};

		while (is_object ($item->parent)) {
			if ($item->parent->id == $parent) {
				return true;
			}
			$item =& $item->parent;
		}
		return false;
	}
}

/**
 * Adds an HTTP header to send with the page.
 *
 * @access public
 * @param string
 */
function page_add_header ($header) {
	$GLOBALS['page']->addHeader ($header);
}

/**
 * Adds an HTML meta tag to the page.
 *
 * @access public
 * @param string
 * @param string
 * @param string
 */
function page_add_meta ($key, $value, $name = 'name') {
	$GLOBALS['page']->addMeta ($key, $value, $name);
}

/**
 * Adds a JavaScript script to the page.
 *
 * @access public
 * @param string
 */
function page_add_script ($script) {
	$GLOBALS['page']->addScript ($script);
}

/**
 * Adds a CSS stylesheet to the page.
 *
 * @access public
 * @param string
 */
function page_add_style ($style) {
	$GLOBALS['page']->addStyle ($style);
}

/**
 * Adds a link tag to the page.
 *
 * @access public
 * @param string
 * @param string
 * @param string
 * @param string
 * @param string
 */
function page_add_link ($rel, $type, $href, $charset = false, $hreflang = false, $name = false) {
	$GLOBALS['page']->addLink ($rel, $type, $href, $charset, $hreflang, $name);
}

/**
 * Sets the page ID to something other than its default.
 * Handy for actions with page aliases.
 *
 * @access public
 * @param string
 */
function page_id ($id) {
	$GLOBALS['page']->id = $id;
}

/**
 * Sets the title of the page.
 *
 * @access public
 * @param string
 */
function page_title ($title) {
	$GLOBALS['page']->title = $title;
}

/**
 * Sets the head_title of the page.
 *
 * @access public
 * @param string
 */
function page_head_title ($title) {
	$GLOBALS['page']->head_title = $title;
}

/**
 * Sets the nav_title of the page.
 *
 * @access public
 * @param string
 */
function page_nav_title ($title) {
	$GLOBALS['page']->nav_title = $title;
}

/**
 * Sets the description of the page.
 *
 * @access public
 * @param string
 */
function page_description ($description) {
	if (! empty ($description)) {
		$GLOBALS['page']->addMeta ('description', $description);
	}
}

/**
 * Sets the keywords of the page.
 *
 * @access public
 * @param string
 */
function page_keywords ($keywords) {
	if (! empty ($keywords)) {
		$GLOBALS['page']->addMeta ('keywords', $keywords);
	}
}

/**
 * Sets the below_page property of the page.  This sets the position of
 * the page within the web site breadcrumbs.
 *
 * @access public
 * @param string
 * @param string
 */
function page_below ($ref) {
	$GLOBALS['page']->below_page = $ref;
}

/**
 * Sets the template to use to render the page.
 *
 * @access public
 * @param string
 */
function page_template ($value) {
	$GLOBALS['page']->template = $value;
}

/**
 * Sets the template set (ie. theme) to use to render the page.
 *
 * @access public
 * @param string
 */
function page_template_set ($value) {
	$GLOBALS['page']->template_set = $value;
}

/**
 * Alias of the getSection() method.  Returns the first section page that
 * the current page is a child of.
 *
 * @access public
 * @return string
 */
function page_get_section () {
	return $GLOBALS['page']->getSection ();
}

/**
 * Alias of the getTitle() method.
 *
 * @access public
 * @param string
 * @return string
 */
function page_get_title ($page = false) {
	return $GLOBALS['page']->getTitle ($page);
}

/**
 * Alias of the getPageId() method.
 *
 * @access public
 * @param string
 * @return string
 */
function page_get_id ($page = false) {
	return $GLOBALS['page']->getPageId ($page);
}

/**
 * Alias of the getNavTitle() method.
 *
 * @access public
 * @param string
 * @return string
 */
function page_get_nav_title ($page = false) {
	return $GLOBALS['page']->getNavTitle ($page);
}

/**
 * Alias of the isParent() method.
 *
 * @access public
 * @param string
 * @return boolean
 */
function page_is_parent ($parent) {
	return $GLOBALS['page']->isParent ($parent);
}

/**
 * Adds an onload="" handler to the page.  This can be rendered in your XT
 * templates in the onload attribute of your HTML body tag, by inserting the
 * value "${onload}".
 *
 * @access public
 * @param string
 */
function page_onload ($value) {
	global $page;
	if (! empty ($page->onload)) {
		$page->onload .= '; ' . $value;
	} else {
		$page->onload = $value;
	}
}

/**
 * Adds an onunload="" handler to the page.  This can be rendered in your XT
 * templates in the onunload attribute of your HTML body tag, by inserting the
 * value "${onunload}".
 *
 * @access public
 * @param string
 */
function page_onunload ($value) {
	global $page;
	if (! empty ($page->onunload)) {
		$page->onunload .= '; ' . $value;
	} else {
		$page->onunload = $value;
	}
}

/**
 * Adds an onfocus="" handler to the page.  This can be rendered in your XT
 * templates in the onfocus attribute of your HTML body tag, by inserting the
 * value "${onfocus}".
 *
 * @access public
 * @param string
 */
function page_onfocus ($value) {
	global $page;
	if (! empty ($page->onfocus)) {
		$page->onfocus .= '; ' . $value;
	} else {
		$page->onfocus = $value;
	}
}

/**
 * Adds an onblur="" handler to the page.  This can be rendered in your XT
 * templates in the onblur attribute of your HTML body tag, by inserting the
 * value "${onblur}".
 *
 * @access public
 * @param string
 */
function page_onblur ($value) {
	global $page;
	if (! empty ($page->onblur)) {
		$page->onblur .= '; ' . $value;
	} else {
		$page->onblur = $value;
	}
}

/**
 * Adds an onclick="" handler to the page.  This can be rendered in your XT
 * templates in the onclick attribute of your HTML body tag, by inserting the
 * value "${onclick}".
 *
 * @access public
 * @param string
 */
function page_onclick ($value) {
	global $page;
	if (! empty ($page->onclick)) {
		$page->onclick .= '; ' . $value;
	} else {
		$page->onclick = $value;
	}
}

?>
