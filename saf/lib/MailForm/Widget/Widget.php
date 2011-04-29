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
// The base class for creating form widgets.  Used only for extending.
//

loader_import ('saf.MailForm.Rule');

/**
	 * The base class for creating form widgets.  Used only for extending.
	 * 
	 * Rules for creating custom widgets:
	 * - must define some field (can be a blank hidden field) with their own $w->name
	 * - must extend MF_Widget and must call parent::MF_Widget in their constructor method
	 * - must use other widgets to create "compound" or custom widgets from, and must
	 *   handle the task of collecting the values of those internal widgets to expose
	 *   a singular $w->data_value when requested (this is simplified, don't worry :)
	 * - must dynamically load any other widgets called, using the $GLOBALS['loader']
	 *   object to do so.
	 * 
	 * New in 1.2:
	 * - Made some clarifications in the docs regarding setValues() and setValue(),
	 *   which are rather ambiguous.  How to remember the difference: setValues as in
	 *   ALL possible values (pleural), setValue as in THE actual value (singular).
	 * 
	 * New in 1.4:
	 * - Changed var $value; to var $value = array ();, so that empty fields of certain
	 *   types don't show errors.
	 * - Added a setDefault() method which is handy for other widgets to inherit, since
	 *   setting the default value might not always be as simple as setting the
	 *   $default_value property.
	 * 
	 * New in 1.6:
	 * - Added a new $passover_isset property.  Read below for details.
	 * 
	 * New in 1.8:
	 * - Deprecated the validation() method in favour of an addRule() method, which
	 *   allows a widget to have more than one validation rule.  This also allows
	 *   each rule to have its own error message.
	 * - Added a validate() method which is called by the MailForm->invalid()
	 *   method.
	 * - Validation rules are now their own class, called MailFormRule, which are
	 *   accessed indirectly through this class.  For more info, see
	 *   saf.MailForm.Rule.
	 * 
	 * New in 2.0:
	 * - Added new methods: addRule() and validate().
	 * - Added new properties: $type and $rules.
	 * - Deprecated one method, validation(), and one property, $rule.
	 * 
	 * New in 2.2:
	 * - Added an $_attrs property and three new methods, attr(), unsetAttr(), and getAttrs().
	 * - Deprecated the $extra property in favour of the new property and methods just
	 *   added.  Note: Migration to these new methods is not complete in all of the widgets
	 *   just yet, and $extra will still work for some time, so the $extra method should
	 *   still be used right now, however new widgets may or may not support the $extra
	 *   property.
	 * 
	 * New in 2.4:
	 * - Added a $label_template property.
	 * 
	 * New in 2.6:
	 * - Added an $invalid property and an invalid() method.
	 *
	 * New in 2.8:
	 * - Added docs for the implied $nullable property, and made getValue() return null
	 *   properly according to the $nullable setting.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget ('name');
	 * $widget->addRule ('is "foo"', 'You must enter "foo" to pass!');
	 * $widget->setValue ('foo');
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.8, 2003-11-07, $Id: Widget.php,v 1.7 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget {
	/**
	 * The name of the widget.  $name is actually an alias for
	 * $_attrs['name'].
	 * 
	 * @access	public
	 * 
	 */
	var $name;

	/**
	 * The display text of the widget.
	 * 
	 * @access	public
	 * 
	 */
	var $display_value;

	/**
	 * The default value of the widget.
	 * 
	 * @access	public
	 * 
	 */
	var $default_value;

	/**
	 * The possible values of the widget.  Can be a string or hash.
	 * 
	 * @access	public
	 * 
	 */
	var $value = array (); // string or hash of possible values

	/**
	 * The actual value of the widget.  Could be user input value or
	 * same as $default_value.
	 * 
	 * @access	public
	 * 
	 */
	var $data_value; // actual value (could be $default_value or user input)

	/**
	 * The validation rule for this widget.  Note: This property is
	 * deprecated in favour of the new $rules property.
	 * 
	 * @access	public
	 * 
	 */
	var $rule;

	/**
	 * The error message to display in place of the form message if this
	 * widget is invalid.  This property is is not used to set the error messages,
	 * see saf.MailForm.Rule for that, but it is still used internally by
	 * saf.MailForm.
	 * 
	 * @access	private
	 * 
	 */
	var $error_message;

	/**
	 * This indicates to the MailForm validation routine whether or not to
	 * skip checking if isset() on this widget.  This is useful for checkboxes, file
	 * uploads, reset buttons, multiple-choice select boxes, pseudo widgets (see
	 * the Hiddenswitch widget for an example of this), and sub-classes of these
	 * (for example, the Allow widget is a sub-class of the Checkbox widget).  This
	 * is set to false by default.
	 * 
	 * @access	public
	 * 
	 */
	var $passover_isset = false;

	/**
	 * Contains the widget type name (ie. 'textarea' for a MF_Widget_textarea
	 * object).  Used only by saf.Database.Table to keep track of things in the
	 * _makeWidget() method.
	 * 
	 * @access	public
	 * 
	 */
	var $type;

	/**
	 * Contains a list of rules to apply to this widget.  See saf.MailForm.Rule
	 * for more information.
	 * 
	 * @access	public
	 * 
	 */
	var $rules = array ();

	/**
         * Name of a filter function to apply on the widget value
	 *
         * @access public
         */
	var $filter;

	/**
         * Used to specify a lib to import for filter function
	 *
         * @access public
         */
	var $import_filter;

	/**
	 * This contains a list of attributes of the HTML tag.
	 * 
	 * @access	private
	 * 
	 */
	var $_attrs = array ();

	/**
	 * Template to use to display the label value of the widget when
	 * $generate_html is set to true (referring the parameter of the display()
	 * method).  Defaults to '##display_value##', which will output exactly
	 * what the display value contains.  Note to widget developers: The
	 * templates should be evaluated for substitutions only, so that this
	 * doesn't introduce much overhead for large forms.
	 * 
	 * @access	private
	 * 
	 */
	var $label_template = '{display_value}';

	/**
	 * Whether this field is invalid.  Used in the display() method
	 * to set class="invalid" on the field label.
	 * 
	 * @access	public
	 * 
	 */
	var $invalid = false;

	/**
	 * When a widget is attached to a form (via addWidget()), which is
	 * not always necessarily the case, then this will contain a reference to
	 * that form object.
	 * 
	 * @access	public
	 * 
	 */
	var $form = false;

	/**
	 * Determines whether an empty value should be returned as NULL or as an
	 * empty string by getValue().  Default is the empty string, since it is
	 * common to specify NOT NULL on database tables.
	 *
	 * @access	public
	 *
	 */
	var $nullable = false;

	/**
	 * If this is set to true, the widget will be automatically hidden from
	 * users whose 'browse_level' preference isn't set to 'advanced'.
	 *
	 * @access	public
	 *
	 */
	var $advanced = false;

	/**
	 * Reference shows on the right in an added table column for things like
	 * translating from an original document.
	 *
	 * @access	public
	 *
	 */
	var $reference = false;

	/**
	 * Indicate to the user this is a requiried field
	 *
	 * Automatically set is 'not empty' rule applied.
	 * Each widget must take care of this attribute.
	 */
	var $required = false;

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget ($name) {
		$this->name = $name;
		$this->display_value = ucwords (str_replace ('_', ' ', $name));
		$this->alt =& $this->display_value;
		$this->passover_isset = false;
		$this->error_message = '';

		// set up the $_attrs list
		$this->_attrs['id'] =& $name;
		$this->_attrs['name'] =& $name;
	}

	/**
	 * Sets the validation $rule for this widget.  Note: This method is
	 * deprecated and only wraps around the addRule() method anyway.  Please use
	 * addRule() instead, since this method will be removed in a near-future
	 * release.
	 * 
	 * @access	public
	 * @param	string	$rule
	 * 
	 */
	function validation ($rule) {
		$this->addRule ($rule);
	}

	/**
	 * Adds a validation rule to the list of $rules.
	 * 
	 * @access	public
	 * @param	string	$rule
	 * @param	string	$msg
	 * @return	boolean
	 * 
	 */
	function addRule ($rule, $msg = '') {
		if (in_array ($rule, array ('not empty', 'email'))) {
			$this->required = true;
		}
		$this->rules[] = new MailFormRule ($rule, $this->name, $msg);
	}

	/**
	 * Validates the widget against its set of $rules.  Returns false
	 * on failure to pass any rule.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	object	$form
	 * @param	object	$cgi
	 * @return	boolean
	 * 
	 */
	function validate ($value, $form, $cgi) {
		//if ($this->name == 'password_verify') {
		//	echo $this->name . ': ' . count ($this->rules);
		//}
		if ($this->length > 0) {
			$this->length = (int) $this->length;
			$r = new MailFormRule (
				'length "' . $this->length . '-"',
				$this->name,
				intl_get ('The "{name}" field exceeds its maximum size of {length}.', $this)
			);
			array_unshift ($this->rules, $r);
		}

		foreach (array_keys ($this->rules) as $k) {
			$rule =& $this->rules[$k];  
			if (! $rule->validate ($value, $form, $cgi)) {
				$this->error_message = $rule->msg;
				return false;
			}
		}
		return true;
	}

	/**
	 * Sets the *POSSIBLE* values for this widget.  If $value
	 * is given, sets $this->value as a hash, otherwise, as a string.
	 * Please Note: No simple arrays allowed, or the numeric keys will
	 * be used as the value property in the HTML output.
	 * 
	 * @access	public
	 * @param	string	$key
	 * @param	string	$value
	 * 
	 */
	function setValues ($key, $value = '') {
		if (! empty ($value)) {
			$this->value[$key] = $value;
		} elseif (is_string ($key) && strpos ($key, 'eval:') === 0) {
			eval (CLOSE_TAG . OPEN_TAG . ' $this->value = ' . substr ($key, 5) . '; ' . CLOSE_TAG);
		} else {
			// could be a string or a hash (but no simple arrays!)
			$this->value = $key;
		}
	}

	/**
	 * Sets the *ACTUAL* value for this widget.  An optional second
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
		if (strpos ($value, 'eval:') === 0) {
			eval (CLOSE_TAG . OPEN_TAG . ' $this->data_value = ' . substr ($value, 5) . '; ' . CLOSE_TAG);
		} else {
			$this->data_value = $value;
		}
		$this->data_value = $this->applyFilter ($this->data_value);
	}

	/**
	 * Transform the given value with the widget filter
	 *
         * @access protected
         */
	function applyFilter ($value) {
		if ($this->filter) {
			if ($this->import_filter) {
				loader_import ($this->import_filter);
			}
			return call_user_func ($this->filter, $value);
		}
		else {
			return $value;
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
			//echo '<p><strong>' . $this->name . ' NOT CGI</strong></p>';
			if (! isset ($this->data_value)) {
				return $this->default_value;
			} else {
				return $this->data_value;
			}
		} else {
			//echo '<p><strong>' . $this->name . ' FROM CGI</strong></p>';

			//$this->data_value = $cgi->{$this->name};
			if (isset ($cgi->{$this->name})) {
				if ($this->nullable && empty ($cgi->{$this->name})) {
					return null;
				}
				return $this->applyFilter ($cgi->{$this->name});
			} else {
				return null;
			}

		}
	}

	/**
	 * Sets the default value for the widget.
	 * 
	 * @access	public
	 * @param	string	$value
	 * 
	 */
	function setDefault ($value) {
		if (strpos ($value, 'eval:') === 0) {
			eval (CLOSE_TAG . OPEN_TAG . ' $this->default_value = ' . substr ($value, 5) . '; ' . CLOSE_TAG);
		} else {
			$this->default_value = $value;
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
		if (! isset ($this->data_value)) {
			$this->data_value = $this->default_value;
		}
		// let the subclasses define most of this one
	}

	/**
	 * This is the accessor method for setting and getting the value of
	 * any attribute of the tag, including 'method' and 'action'.  This will
	 * replace the $extra property, which is henceforth deprecated.  If you call
	 * this method and provide no $value, you are using it as a 'getter', as in
	 * you are getting the current value.  If you provide a value, the new value
	 * will be set, so you are acting as a 'setter'.  If you simply specify that
	 * the $value be true, then it will appear filled with its own name (useful
	 * for things like the checked="checked" attribute of a checkbox input field).
	 * 
	 * @access	public
	 * @param	string	$key
	 * @param	string	$key
	 * @return	string
	 * 
	 */
	function attr ($key, $value = false) {
		if ($value === false) {
			return $this->_attrs[$key];
		} else {
			$this->_attrs[$key] = $value;
			return $value;
		}
	}

	/**
	 * Use this method to remove an attribute from the tag
	 * attribute list.  Use this instead of passing a false value to attr(),
	 * because a false value essentially means "return the current value"
	 * in that method.  This method returns the old value of the attribute
	 * being unset.
	 * 
	 * @access	public
	 * @param	string	$key
	 * @return	string
	 * 
	 */
	function unsetAttr ($key) {
		$old = $this->_attrs[$key];
		unset ($this->_attrs[$key]);
		return $old;
	}

	/**
	 * Returns a list of all of the attributes of this object's HTML tag
	 * in a string ready to be concatenated into the actual rendered tag output.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function getAttrs () {
		$res = '';
		foreach ($this->_attrs as $key => $value) {
			if ($value === false) {
				continue;
			} elseif ($value === true) {
				$res .= $key . '="' . $key . '" ';
			} else {
				$res .= $key . '="' . $value . '" ';
			}
		}
		return $res;
	}

	/**
	 * Returns a ' class="invalid"' string if the widget's $invalid
	 * property is set to true, or an empty string otherwise.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function invalid () {
		if ($this->invalid) {
			return ' class="invalid"';
		}
		return '';
	}

	/**
         * Returns names of classes to apply to the widget. For instance,
	 * advanced and required.
         *
         * @access protected
         * @return string
         */
	function getClasses () {
		$c = array ();
		if ($this->advanced) {
			$c[] = "advanced";
		}
		if ($this->required) {
			$c[] = "required";
		}
		if (empty ($c)) {
			return '';
		}
		else {
			return ' class="' . implode (" ", $c) . '"';
		}
	}

	function changeType ($newType, $extra = array ()) {
		if (! strstr ($newType, '.')) {
			$path = 'saf.MailForm.Widget.' . ucfirst ($newType);
		} else {
			$path = $newType;
			$info = pathinfo ($newType);
			$newType = strtolower ($info['extension']);
		}

		loader_import ($path);

		$class = 'MF_Widget_' . $newType;

		$obj = new $class ($this->name);

		foreach (get_object_vars ($this) as $key => $value) {
			if ($key == 'passover_isset') {
				continue;
			}
			$obj->{$key} = $value;
		}

		foreach ($extra as $key => $value) {
			$obj->{$key} = $value;
		}

		return $obj;
	}
}



?>
