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
// Dirlist widget.  Displays a select box with a list of files from a
// specific folder.
//

/**
	 * Dirlist widget.  Displays a select box with a list of files from a
	 * specific folder.
	 * 
	 * Note: If $show_viewbutton is true, requires the $intl object.
	 * 
	 * New in 1.2:
	 * - Added a $recursive option.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_dirlist ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->setValue ('foo');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2002-04-27, $Id: Dirlist.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_dirlist extends MF_Widget {
	/**
	 * Contains a list of valid file extensions to display from the
	 * directory.
	 * 
	 * @access	public
	 * 
	 */
	var $extensions = array ();

	/**
	 * Contains the directory to read the list of files from.
	 * 
	 * @access	public
	 * 
	 */
	var $directory;

	/**
	 * Contains the data value of this widget.
	 * 
	 * @access	private
	 * 
	 */
	var $data_value_DIRLIST;

	/**
	 * A way to pass extra parameters to the HTML form tag, for
	 * example 'enctype="multipart/formdata"'.
	 * 
	 * @access	public
	 * 
	 */
	var $extra = '';

	/**
	 * Determines whether or not to display a 'view' link next to
	 * the select box, which will pop up a view of the item from the list.
	 * 
	 * @access	public
	 * 
	 */
	var $show_viewbutton;

	/**
	 * Contains the web directory to access the list of files from.
	 * 
	 * @access	public
	 * 
	 */
	var $web_path;

	/**
	 * Contains the name property of the current form.  Required only if
	 * $show_viewbutton is true.
	 * 
	 * @access	public
	 * 
	 */
	var $formname;

	/**
	 * Whether or not the list should display recursively into sub-directories.
	 * 
	 * @access	public
	 * 
	 */
	var $recursive;

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'dirlist';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_dirlist ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);

		// load Hidden and Select widgets, on which this widget depends
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Hidden');
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Select');
		$GLOBALS['loader']->import ('saf.File.Directory');

		// initialize custom widget settings
		$this->data_value_DIRLIST = '';
		$this->directory = '.';
		$this->show_viewbutton = false;
		$this->web_path = '';
		$this->formname = 'mainform';
		$this->recursive = 0;
	}

	/**
	 * Sets the actual value for this widget.  An optional second
	 * parameter can be passed, which is unused here, but can be used in
	 * complex widget types to assign parts of a value and piece it together
	 * from multiple physical form fields.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	string	$inner_component
	 * 
	 */
	function setValue ($value = '', $inner_component = '') {
		if (! empty ($inner_component)) {
			$this->{'data_value_' . $inner_component} = $value;
		} else {
			$this->data_value_DIRLIST = $value;
		}
	}

	/**
	 * Fetches the actual value for this widget.
	 * 
	 * @access	public
	 * @param	object	$cgi
	 * @return	string
	 * 
	 */
	function getValue ($cgi = '') {
		if (! is_object ($cgi)) {
			return $this->data_value;
		} else {
			return $cgi->{'MF_' . $this->name . '_DIRLIST'};
		}
	}

	/**
	 * Returns the display HTML for this widget.  The optional
	 * parameter determines whether or not to automatically display the widget
	 * nicely, or whether to simply return the widget (for use in a template).
	 * 
	 * @access	public
	 * @param	boolean	$generate_html
	 * @return	string
	 * 
	 */
	function display ($generate_html = 0) {
		$_dl = new MF_Widget_select ('MF_' . $this->name . '_DIRLIST');
		$_dl->extra = $this->extra;
		$dir = new Dir ($this->directory);
		if ($this->recursive == 0) {
			$list = $dir->read_all ();
			$goodlist = array ();
			$dir->close ();
			foreach ($list as $file) {
				if (
						(! preg_match ('/^\.ht/i', $file)) &&
						(preg_match ('/\.(' . join ('|', $this->extensions) . ')$/i', $file) || count ($this->extensions) == 0) &&
						($file != '.') &&
						($file != '..')
					) {
					$goodlist[$file] = $file;
				}
			}
		} else {
			$goodlist = array ();
			// recurse
			$list = $dir->find ('*', $this->directory, 1);
			for ($i = 0; $i < count ($list); $i++) {
				if (
					(! @is_dir ($list[$i])) &&
					(! preg_match ('/^\.ht/i', $list[$i])) &&
					(preg_match ('/\.(' . join ('|', $this->extensions) . ')$/i', $list[$i]) || count ($this->extensions) == 0) &&
					($list[$i] != '.') &&
					($list[$i] != '..')
				) {
					$list[$i] = preg_replace ('/^' . preg_quote ($this->directory . '/', '/') . '/', '', $list[$i]);
					$goodlist[$list[$i]] = $list[$i];
				}
			}
		}
		$_dl->setValues ($goodlist);
		$_dl->data_value = $this->data_value_DIRLIST;

		$_dirlist = new MF_Widget_hidden ($this->name);

		$data = '';

		if ($this->show_viewbutton) {
			global $intl;
			$data .= '<script language="JavaScript">
<!--

function ' . $this->name . '_preview (name, params, formname) {
	// get src from form widget
	path = "' . $this->web_path . '/' . '" + document.forms[formname].elements[name].options[document.forms[formname].elements[name].selectedIndex].value;

	pop = window.open (\'\', name, params);
	pop.document.open ();
	pop.document.write (\'<link rel="stylesheet" type="text/css" href="css/site.css" />\');
	pop.document.write (\'<div align="center">\');
	pop.document.write (\'<img src="\' + path + \'" alt="\' + name + \'" border="0" />\');
	pop.document.write (\'<br /><br /><a href="#" onclick="window.close ()">' . $intl->get ('Close Window') . '</a></div>\');
}

// -->
</script>';
			$showbutton = '&nbsp;<a href="#" onclick="' . $this->name . '_preview (\'MF_' . $this->name . '_DIRLIST\', \'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,dependent=no,fullscreen=no,width=300,height=300,top=50,left=160\', \'' . $this->formname . '\')">' . $intl->get ('Preview') . '</a>';
		} else {
			$showbutton = '';
		}

		if ($generate_html) {
			$data .= $_dirlist->display (0) . "\n";
			$data .= "\t<tr>\n\t\t<td>" . '<label for="' . $this->name . '"' . $this->invalid () . '>' . $this->display_value . "</label></td>\n\t\t<td>" . 
				$_dl->display (0) . $showbutton . "</td>\n\t</tr>\n";
		} else {
			$data .= $_dirlist->display (0);
			$data .= $_dl->display (0);
			$data .= $showbutton;
		}
		return $data;
	}
}



?>