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
// Text widget.  Displays an HTML <input type="text" /> form field.
//

/**
	 * Text widget.  Displays an HTML <input type="text" /> form field.
	 * 
	 * New in 1.2:
	 * - Made sure to call parent::display() in the display() method.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_text ('name');
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
	 * @version	1.2, 2002-05-03, $Id: Shapeshifter.php,v 1.1 2004/06/21 22:13:00 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_shapeshifter extends MF_Widget {
	/**
	 * A way to pass extra parameters to the HTML form tag, for
	 * example 'enctype="multipart/formdata"'.
	 * 
	 * @access	public
	 * 
	 */
	var $extra = '';

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'shapeshifter';

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
		parent::display ($generate_html);
		global $intl, $simple;
		$attrstr = $this->getAttrs ();

		global $cgi;
		if (! isset ($cgi->format) || empty ($cgi->format)) {
			$val = $this->form->widgets['format']->getValue ();
			if (empty ($val)) {
				$cgi->format = 'image';
			} else {
				$cgi->format = $val;
			}
		}

		switch ($cgi->format) {
			case 'image':
				$obj =& $this->changeType ('imagechooser');
				$obj->alt = 'Image File';
				$obj->path = '/inc/app/sitebanner/data';
				$obj->webpath = '/inc/app/sitebanner/data';
				return $obj->display ($generate_html);
				break;
			case 'html':
				$obj =& $this->changeType ('textarea');
				$obj->alt = 'HTML Code';
				$obj->labelPosition = 'left';
				$obj->cols = 40;
				$obj->rows = 10;
				return $obj->display ($generate_html);
				break;
			case 'text':
				$obj =& $this->changeType ('textarea');
				$obj->alt = 'Ad Text';
				$obj->labelPosition = 'left';
				$obj->cols = 40;
				$obj->rows = 5;
				return $obj->display ($generate_html);
				break;
			case 'external':
				$obj =& $this->changeType ('text');
				$obj->alt = 'External Link';
				$obj->extra = 'size="40"';
				return $obj->display ($generate_html);
				break;
			case 'adsense':
				$obj =& $this->changeType ('textarea');
				$obj->alt = 'Google(TM) AdSense Code';
				$obj->labelPosition = 'left';
				$obj->cols = 40;
				$obj->rows = 10;
				return $obj->display ($generate_html);
				break;
		}
	}
}



?>