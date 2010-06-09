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
// Tied select widget.  Ties the values of one select widget to the
// actions of another, allowing the option list to change dynamically
// based on previous choices made in the form.
//

/**
	 * Tied select widget.  Ties the values of one select widget to the
	 * actions of another, allowing the option list to change dynamically
	 * based on previous choices made in the form.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $tied = new MF_Widget_select ('tied');
	 * $widget = new MF_Widget_tie ('name');
	 * $widget->tie = 'tied';
	 * 
	 * $tied->extra = 'onchange="changeOptions (this.form, \'name\', \'tied\'); reLoad ()"';
	 * 
	 * // now optional -- called automatically by display()
	 * echo $widget->includeJS ();
	 * 
	 * $list = array (
	 * 	'one' => array (
	 * 		'a' => 'A',
	 * 		'b' => 'B',
	 * 	),
	 * 	'two' => array (
	 * 		'c' => 'C',
	 * 		'd' => 'D',
	 * 	),
	 * )
	 * 
	 * foreach ($list as $item => $list) {
	 * 	$tied->setValues ($item, ucfirst ($item));
	 * 	$widget->ties[$item] = $list;
	 * }
	 * 
	 * $widget->validation ('is "foo"');
	 * $widget->setValue ('foo');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * 
	 * ?>
	 * </code>
	 * 
	 * Alternately, via INI settings:
	 *
	 * <code>
	 *
	 * [tied]
	 *
	 * type = select
	 * setValues = "eval: assoscify (range ('a', 'g'))"
	 * extra = "onchange=`changeOptions (this.form, 'name', 'tied'); reLoad ()`"
	 *
	 * [name]
	 *
	 * type = tie
	 * setValues = "eval: array ('a' => assocify (array ('a','b','c')), etc.)"
	 * tie = tied
	 *
	 * </code>
	 *
	 * Note that the eval options would presumably be replaced with a call to an
	 * external function in real-life scenarios.
	 *
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2002-09-26, $Id: Tie.php,v 1.5 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_tie extends MF_Widget {
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
	var $type = 'tie';

	/**
	 * The name of the field to be "tied" to.
	 * 
	 * @access	public
	 * 
	 */
	var $tie = false;

	/**
	 * The name of the form.  Note: If this is not the first form
	 * on the page, then a form name is required (must also set the $name
	 * property of the MailForm object).
	 * 
	 * @access	public
	 * 
	 */
	var $formname = false;

	/**
	 * The path to the tie.js JavaScript file, which contains
	 * the JavaScript components to this widget.  This is determined
	 * automatically in the constructor.
	 * 
	 * @access	public
	 * 
	 */
	var $jsFile = false;

	/**
	 * This is the list of values to display for each value of
	 * the widget this widget is tied to.  Ordinary select widgets simply
	 * have a $values associative array, but the tie widget has this which
	 * is a $values array for each value of the other widget.
	 * 
	 * @access	public
	 * 
	 */
	var $ties = array ();

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_tie ($name) {
		global $_SERVER;
		//echo dirname ($_SERVER['DOCUMENT_ROOT'] . '/index') . '<br />' . dirname (__FILE__);
		//exit;
		$this->jsFile = str_replace (dirname ($_SERVER['DOCUMENT_ROOT'] . '/index'), '', dirname (__FILE__)) . '/tie.js';
		parent::MF_Widget ($name);
	}

	/**
	 * Sets the values to display for the specified value of the
	 * other widget, which is specified in $name parameter.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	associative array	$values
	 * 
	 */
	function setValues ($name, $values = false) {
		if ($values === false) {
			$this->ties = $name;
		} else {
			$this->ties[$name] = $values;
		}
	}

	/**
	 * Generates the necessary HTML to include the JavaScript components
	 * to this widget.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function includeJS () {
		static $incl = false;
		if (! $incl) {
			$incl = true;
			return sprintf ("<script language=\"JavaScript1.1\" src=\"%s\"></script>\n", $this->jsFile);
		}
		return '';
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
		global $intl, $simple;
		if (! isset ($this->data_value)) {
			$this->data_value = $this->default_value;
		}
		$data = '';

		echo $this->includeJS ();

		$data .= "<script language=\"JavaScript1.1\">\n<!--\n\n";
		$data .= 'options[\'' . $this->name . '\'] = new Array ();' . "\n";
		$data .= 'selected[\'' . $this->name . '\'] = new Array ();' . "\n";
		foreach ($this->ties as $tie => $values) {
			$data .= 'options[\'' . $this->name . '\'][\'' . $tie . '\'] = new Array ();' . "\n";
			$count = 0;
			foreach ($values as $key => $value) {
				$data .= 'options[\'' . $this->name . '\'][\'' . $tie . '\'][' . $count . '] = new Option (\'' . $value . '\', \'' . $key . '\');' . "\n";
				$count++;
			}
		}
		$data .= "\n// -->\n</script>\n";

		if ($generate_html) {
			$data .= "\t" . '<tr>' . "\n\t\t" . '<td class="label"><label for="' . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
				'<td class="field"><select name="' . $this->name . '" ' . $this->extra . ' >' . "\n";
			foreach ($this->value as $value => $display) {
				if ((string) $value == (string) $this->data_value) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				$data .= "\t" . '<option value="' . $value . '"' . $selected . '>' . $display . '</option>' . "\n";
			}
			$data .= '</select></td>' . "\n\t" . '</tr>' . "\n";
		} else {
			$data .= '<select name="' . $this->name . '" ' . $this->extra . ' >' . "\n";
			foreach ($this->value as $value => $display) {
				if ((string) $value == (string) $this->data_value) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				$data .= "\t" . '<option value="' . htmlentities_compat ($value, ENT_COMPAT, $intl->charset) . '"' . $selected . '>' . $display . '</option>' . "\n";
			}
			$data .= '</select>';
		}

		$data .= "<script language=\"javascript1.1\">\n<!--\n\n// on document loading, this will execute and give the 'column' and 'order' boxes initial values\n// this must be called after the form is loaded\n";
		if (! $this->formname) {
			$data .= "changeOptions (document.forms[0], '" . $this->name . "', '" . $this->tie . "');\n";
		} else {
			$data .= "changeOptions (document.forms['" . $this->formname . "'], '" . $this->name . "', '" . $this->tie . "');\n";
		}
		$data .= "\n// -->\n</script>\n";

		return $data;
	}
}



?>