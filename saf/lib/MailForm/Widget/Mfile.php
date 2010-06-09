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
// Mfile widget.  Displays an HTML <input type="file" /> form field.
// that allow multpile files upload.
//

/**
	 * File widget.  Displays an HTML <input type="file" /> form field
	 * with possibility to upload multiple files.
	 * 
	 * New in 1.2:
	 * - Added a move() method, to move the temp file to a more permanent location.
	 *   move() also calls is_uploaded_file for you, but since the $cgi object
	 *   does also it's duplicate code.  We do need it though to make sure we don't
	 *   try talking to an object under $cgi that doesn't exist.
	 *   Calls is_uploaded_file() internally, so there's no need to do it again.
	 * - Added a $path property, so that the path to save files to can be pre-specified.
	 * 
	 * New in 1.4:
	 * - Added a $web_path property, to distinguish between the filesystem path and the
	 *   web folder path.
	 * 
	 * New in 1.6:
	 * - Added a constructor method to set the $passover_isset value to true, which
	 *   is inherited from MF_Widget.
	 * 
	 * New in 1.8:
	 * - Added $mode, $user, and $group properties, which set the mode, user, and
	 *   group information for newly uploaded files during the move() method.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_file ('name');
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
	 * @version	1.8, 2002-08-05, $Id: File.php,v 1.7 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_mfile extends MF_Widget {
	/**
	 * A way to pass extra parameters to the HTML form tag, for
	 * example 'enctype="multipart/form-data"'.
	 * 
	 * @access	public
	 * 
	 */
	var $extra = '';

	/**
	 * The directory in which to place the uploaded file.
	 * 
	 * @access	public
	 * 
	 */
	var $path = '';

	/**
	 * The web path to the directory in which to place the uploaded file.
	 * 
	 * @access	public
	 * 
	 */
	var $web_path = '';

	/**
	 * Specifies a mode to chmod() the newly uploaded file to during
	 * the move() method.  The mode is also known as the permissions of the
	 * file.  Must be set as a 4-digit octal value with the first digit on
	 * the left being a zero (0), for example 0755.  See the PHP documentation
	 * for the chmod() function for more information.
	 * 
	 * @access	public
	 * 
	 */
	var $mode = false;

	/**
	 * Specifies a user to chown() the newly uploaded file to during
	 * the move() method.  This is not likely to work on most systems, but
	 * it exists for the few strange (and highly suspect) configurations
	 * where it will.  See the PHP documentation for the chown() function
	 * for more information.
	 * 
	 * @access	public
	 * 
	 */
	var $user = false;

	/**
	 * Specifies a group to chgrp() the newly uploaded file to during
	 * the move() method.  Can only be changed to any group that the PHP user
	 * belongs to.  See the PHP documentation for the chgrp() function for
	 * more information.
	 * 
	 * @access	public
	 * 
	 */
	var $group = false;

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'file';

	/**
	 * This determines whether to provide a "Clear" button when a file exists
	 * in an edit form, so that the current value can be reset without being
	 * replaced.
	 *
	 * @access	public
	 *
	 */
	var $clear = false;

	/**
	 * Constructor Method.  Also sets the $passover_isset property
	 * to false.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_mfile ($name) {
		parent::MF_Widget ($name);
		$this->passover_isset = true;
	}

	/**
	 * Override to eliminate validating length of UploadedFile object.
	 *
	 * @access	public
	 * @param	string	$value
	 * @param	object	$form
	 * @param	object	$cgi
	 * @return	boolean
	 * 
	 */
	function validate ($value, $form, $cgi) {
		$this->length = 0;
		return parent::validate ($value, $form, $cgi);
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
		global $simple;
		$attrstr = $this->getAttrs ();
		$attrstr = preg_replace ('/name="([^"]*)"/', 'name="$1[]"', $attrstr);
		page_add_script (site_prefix () . '/js/jquery.multifile.pack.js');

		if (is_object ($this->form)) {
			$enctype = $this->form->attr ('enctype');
			if (! $enctype) {
				if (! strstr ($this->form->extra, 'enctype')) {
					if (empty ($this->form->extra)) {
						$this->form->extra = 'enctype="multipart/form-data"';
					} else {
						$this->form->extra .= ' enctype="multipart/form-data"';
					}
				}
			}
		}

		if (! empty ($this->data_value)) {
			if (strpos ($this->web_path, '/') !== 0) {
				$prefix = site_prefix () . '/';
			} else {
				$prefix = site_prefix ();
			}
			$url = $prefix . $this->web_path . '/' . $this->data_value;
			while (strpos ($url, '//') === 0) {
				$url = substr ($url, 1);
			}
			if ($this->clear) {
				$file = '&nbsp;<input type="hidden" name="' . $this->name . '_clear" id="' . $this->name . '_clear" value="no" /><input type="submit" value="' . intl_get ('Clear') . '" onclick="document.getElementById (\'' . $this->name . '_clear\').value = (document.getElementById (\'' . $this->name . '_clear\').value == \'yes\') ? \'no\' : \'yes\'; this.value = (this.value == \'' . intl_get ('Clear') . '\') ? \'' . intl_get ('Undo Clear') . '\' : \'' . intl_get ('Clear') . '\'; return false" />';
			} else {
				$file = '<input type="hidden" name="' . $this->name . '_clear" id="' . $this->name . '_clear" value="no" />';
			}
			$file .= '<br /><a href="' . $url . '" target="_blank">' . intl_get ('View current file') . '</a>';
		} else {
			$file = '<input type="hidden" name="' . $this->name . '_clear" id="' . $this->name . '_clear" value="no" />';
		}

		if ($generate_html) {
			return "\t" . '<tr>' . "\n\t\t" . '<td class="label" valign="top"><label for="' . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
				'<td class="field" valign="top"><input type="file" ' . $attrstr . ' value="" class="multi" ' . $this->extra . ' />' . $file . '</td>' . "\n\t" . '</tr>' . "\n";
		} else {
			return '<input type="file" ' . $attrstr . ' value="" class="multi" ' . $this->extra . ' />';
		}
	}

	/**
	 * Calls the move() method of the corresponding UploadedFile
	 * object in the provided $cgi object's properties.  If no $cgi object
	 * is provided, it assumes a global one.  Returns empty on failure or
	 * on 'no file uploaded', and returns the web path to the newly placed
	 * file upon success.
	 * 
	 * @access	public
	 * @param	string	$path
	 * @param	string	$fname
	 * @param	object	$cgi
	 * @return	boolean
	 * 
	 */
	function move ($path = '', $fname = '', $cgi = '') {
		if (empty ($cgi)) {
			// assume a global $cgi object if none is provided
			global $cgi;
		}
		if (empty ($path)) {
			$path = $this->path;
		}
echo "MOVE\n\n\n";
print_r ($cgi);
exit;

		if (! is_uploaded_file ($cgi->{$this->name}->tmp_name) && empty ($cgi->{'MF_' . $this->name . '_HIDDEN'})) {
			return false;
		}

		if (empty ($fname)) {
			$fname = $cgi->{$this->name}->name;
		}

		if (! $this->uploadEmpty && $cgi->{$this->name}->size == 0) {
			if (! empty ($cgi->{'MF_' . $this->name . '_HIDDEN'})) {
				return $cgi->{'MF_' . $this->name . '_HIDDEN'};
			} elseif (@file_exists ($this->path . '/' . $fname)) {
				return $this->web_path . '/' . $fname;
			}
			return false;
		}

		if (! $cgi->{$this->name}->move ($path, $fname)) {
			return false;
		}

		// change the mode of the file, if a preferred one is specified
		if ($this->mode) {
			@umask (0);
			@chmod ($path . '/' . $fname, octdec ((int) $this->mode));
		}

		// change the group ownership of the file, if a preferred one is specified
		if ($this->group) {
			@chgrp ($path . '/' . $fname, $this->group);
		}

		// change the ownership of the file, if a preferred one is specified
		if ($this->user) {
			@chown ($path . '/' . $fname, $this->user);
		}

		return $this->web_path . '/' . $fname;
	}
}



?>
