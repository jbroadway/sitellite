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
// MailForm provides methods for generating, validating and handling HTML
// forms.
//
// resolved tickets:
// #171 nolist error_mode.
//


$GLOBALS['loader']->import ('saf.MailForm.Rule');

/**
	 * MailForm provides methods for generating, validating, and handling web
	 * forms.  Forms can be handled automatically (sent to an email address), or handled
	 * using "action files".  Action files are passed to the handle () method, and can be
	 * used to do anything you want to the form values.  MailForm will generate forms
	 * for you, using a basic HTML tabled layout, which is okay for most applications,
	 * or you can have complete visual customization through form templates.  MailForm
	 * also exposes an EasyText tag, which makes it much quicker to create fully
	 * validating forms in minutes.
	 * 
	 * New in 2.0:
	 * - 16 widgets, including date and time widgets, a directory listing widget, and more.
	 * - 11 different validation rules, including a regular expression rule, cross
	 *   form widget comparisons (good for password verification widgets), and calling
	 *   to pre-defined functions.
	 * - Many speed improvements, such as dynamic loading of widgets, so your form
	 *   only loads the necessary widgets.
	 * - MailForm 2.0 is a complete rewrite, which has a much cleaner API, although it
	 *   is not backward compatible with 1.0, other than through its EasyText tag.
	 * 
	 * Widget Types:
	 * - checkbox
	 * - date
	 * - datetime
	 * - dirlist
	 * - file
	 * - hidden
	 * - image
	 * - multiple (multi-line select box)
	 * - password
	 * - radio
	 * - reset
	 * - select
	 * - submit
	 * - text
	 * - textarea
	 * - time
	 * 
	 * Validation Rules:
	 * - is "value"
	 * - contains "some value"
	 * - regex "some regex"
	 * - equals "anotherfield"
	 * - not empty
	 * - length "6+" (eg: 6, 6+, 6-12, 12-)
	 * - gt "value"
	 * - ge "value"
	 * - lt "value"
	 * - le "value"
	 * - func "func_name" (or function "func_name")
	 * 
	 * Miscellaneous:
	 * - Do not use underscores (_) in the naming of complex widgets (ie. the datetime
	 *   widget).
	 * 
	 * New in 2.2:
	 * - Added an 'Extra' parameter to the EasyText =MailForm tag parameter list, so
	 *   that the File widget can be used without having to resort to coding the form
	 *   in PHP instead.
	 * - Fixed a bug in the EasyText() method, where the value of the Email property
	 *   wasn't being passed on.
	 * 
	 * New in 2.4:
	 * - Changed a reference to "$GLOBALS['PHP_SELF']" to "global $_SERVER; $_SERVER['PHP_SELF']"
	 *   so that it works with register_globals off.
	 * - Added File widgets to the list of widgets skipped on the isset() condition in invalid(),
	 *   because some browsers don't send file fields at all if there is no file.  This may inhibit
	 *   file field validation, but it's necessary due to inconsistencies across browsers.
	 * 
	 * New in 2.6:
	 * - Moved the validation layer into the Widget level.  See saf.MailForm.Widget and
	 *   saf.MailForm.Rule for more info.
	 * - Added a template example to the DocReader docs below.
	 * 
	 * New in 2.8:
	 * - Fixed a bug in getValues() that caused the $vars passed to a validation function
	 *   to be blank.
	 * - Fixed EasyText() to use the new addRule() method instead of validation(), and
	 *   added the ability to include multiple rules for the same widget through EasyText()
	 *   using commas as separators.
	 * - Added a $submitted property which is used by setValues() to keep an accurate
	 *   reading on widgets whose $passover_isset property is set to true.
	 * 
	 * New in 3.0:
	 * - Added an $_attrs property and three new methods, attr(), unsetAttr(), and getAttrs().
	 * - Deprecated the $extra property in favour of the new property and methods just
	 *   added.
	 * - Improved the email output formatting in handle().
	 * 
	 * New in 3.2:
	 * - Deprecated the handle() method in favour of a setHandler(), run(), and onSubmit()
	 *   methods.  These methods make it easier to subclass MailForm to unify the
	 *   location of the form definition and handling.
	 * - Added $sendTo, $sendFrom, $sendExtra, $screenReply, and $handler support
	 *   properties to the new methods.
	 * - Added a parseSettings() method, which makes it much easier to create new
	 *   forms.
	 * - getValues() and invalid() now do not use a passed $cgi object, and instead
	 *   both rely on a global $cgi object, which is set automatically in the Sitellite
	 *   Content Server, Content Manager, and the init.php script in SAF itself, so
	 *   it's reasonable to assume it will be available.  This doesn't affect code
	 *   that still passes the object to invalid(), and the parameter was deprecated
	 *   in getValues() already anyway.
	 * - getValues() now uses a new property called $uploadFiles, which tells it to
	 *   upload files from file widgets for you automatically.  This breaks backward
	 *   compatibility as a default, but you can pass a false value to achieve the
	 *   old behaviour.
	 * 
	 * New in 3.4:
	 * - Added makeAssoc() and rememberFields() methods, for use with the Sitellite
	 *   Content Server form API.
	 * - Added a $title property, which will show in a top-level header above the form
	 *   if provided.
	 * - Added a global formdata_get() function which returns a key/value list from
	 *   the global $formdata array defined in the application property files.
	 * - Added a $uploadFile parameter to run(), which allows finer control over
	 *   file upload handling.
	 * 
	 * New in 3.6:
	 * - Removed the EasyText() and EasyTextInit() methods.
	 * - Removed the saf.MailForm.Wizard package, since SCS now provides a more
	 *   flexible, elegant, and clean, and less buggy way of accomplishing the
	 *   same effect.
	 * - Removed the EasyText widget.
	 * - Added the ability to report all invalid rules at once, instead of just one.
	 *   However, just one is still the default.
	 * 
	 * New in 3.8:
	 * - Added the ability to call addWidget() by specifying the type as a loader path
	 *   to an alternate location.  This does not affect backward compatibility in
	 *   any way.
	 * - Added the ability to call addWidget () by passing a widget object as the
	 *   $type parameter.
	 *
	 * New in 4.0:
	 * - FormHelp ins now integrated into MailForm.  In settings.php, you can
	 *   specify 'formhelp = yes' under [Form] and 'formhelp = Message' under
	 *   any widget and the display of it is automatic.
	 * 
	 * <code>
	 * <?php
	 * 
	 * <?php
	 * 
	 * $form = new MailForm;
	 * $form->template = 'mf2template.spt';
	 * $form->message = 'Please take a moment to fill out our form.';
	 * 
	 * // old way to set attributes
	 * //$form->extra = 'enctype="multipart/form-data"';
	 * 
	 * // new way to set attributes
	 * $form->attr ('enctype', 'multipart/form-data');
	 * 
	 * // build the form
	 * 
	 * // standard usage:
	 * $form->addWidget ('text', 'username');
	 * $form->widgets['username']->display_value = 'Username (min. 6 chars)';
	 * $form->widgets['username']->addRule ('length "6-24"', 'Your username must be between 6 and 24 characters in length.  Please fix this to continue.');
	 * 
	 * // or if you prefer:
	 * $password =& $form->addWidget ('password', 'password');
	 * $password->addRule ('length "6-24"', 'Your password must be between 6 and 24 characters in length.  Please fix this to continue.');
	 * 
	 * $verify =& $form->addWidget ('password', 'verify');
	 * $verify->display_value = 'Verify Password';
	 * $verify->addRule ('equals "password"', 'Your passwords did not match.  Please fix this to continue.');
	 * 
	 * $form->addWidget ('text', 'firstname');
	 * $form->widgets['firstname']->validation ('not empty');
	 * $form->addWidget ('text', 'lastname');
	 * $form->widgets['lastname']->validation ('not empty');
	 * 
	 * $province =& $form->addWidget ('select', 'province');
	 * $province->setValues (array (
	 * 	'BC' => 'British Columbia',
	 * 	'MB' => 'Manitoba',
	 * 	'ON' => 'Ontario',
	 * ));
	 * $province->default_value = 'MB';
	 * 
	 * // the new 'dirlist' widget type
	 * $dlist =& $form->addWidget ('dirlist', 'dltest');
	 * $dlist->directory = 'pix';
	 * $dlist->extensions = array ('jpg', 'gif', 'png');
	 * 
	 * // the new 'date' widget type
	 * $form->addWidget ('date', 'birth-date');
	 * $form->widgets['birth-date']->display_value = 'Birth Date';
	 * 
	 * $textarea =& $form->addWidget ('textarea', 'comments');
	 * $textarea->setValue ('hello world!');
	 * $textarea->addRule ('not empty', 'You must enter a comment.  Please fix this to continue.');
	 * 
	 * $send =& $form->addWidget ('submit', 'send');
	 * $send->setValues ('Submit!');
	 * 
	 * if ($form->invalid ($cgi)) {
	 * 
	 * 	// form is invalid or has not been set yet
	 * 	$form->setValues ($cgi, $invalid_field);
	 * 	echo $form->show ('inc/html/formtemplate.spt');
	 * 
	 * } else {
	 * 
	 * 	// handle submitted form
	 * 
	 * 	if (! $form->handle ('john.luxford@gmail.com', 'Mail Form')) {
	 * 		echo 'Error: ' . $form->error_message;
	 * 	}
	 * 
	 * }
	 * 
	 * ? >
	 * 
	 * -----
	 * inc/html/formtemplate.spt (Note: Replace ** with { and }):
	 * 
	 * <form method="**mailform_method**" action="**mailform_action**" **mailform_extra**>
	 * <p>**mailform_message**</p>
	 * 
	 * <p>Username<br />**username**</p>
	 * <p>Password<br />**password**</p>
	 * <p>Verify Password<br />**verify**</p>
	 * <p>First Name<br />**firstname**</p>
	 * <p>Last Name<br />**lastname**</p>
	 * <p>Province<br />**province**</p>
	 * <p>Pick an image<br />**dltest**</p>
	 * <p>Birthday<br />**birth-date**</p>
	 * <p>Comments<br />**comments**</p>
	 * <p>**send**</p>
	 * 
	 * </form>
	 * 
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	4.0, 2004-01-29, $Id: MailForm.php,v 1.18 2008/05/20 05:43:57 lux Exp $
	 * @access	public
	 * 
	 */

class MailForm {
	/**
	 * Contains the name of the widget that did not validate during
	 * the last call to the invalid () method.
	 * 
	 * @access	public
	 * 
	 */
	var $invalid_field = '';

	/**
	 * The value of the method attribute of the HTML form tag.
	 * $method is actually an alias for $_attrs['method'].
	 * 
	 * @access	public
	 * 
	 */
	var $method;

	/**
	 * The value of the action attribute of the HTML form tag.
	 * $action is actually an alias for $_attrs['action'].
	 * 
	 * @access	public
	 * 
	 */
	var $action;

	/**
	 * An array of form widgets.
	 * 
	 * @access	public
	 * 
	 */
	var $widgets = array ();

	/**
	 * The optional template file or data used to customize the look
	 * of the form.
	 * 
	 * @access	public
	 * 
	 */
	var $template;

	/**
	 * The title to be displayed at the top of the form.
	 * 
	 * @access	public
	 * 
	 */
	var $title;

	/**
	 * The initial message to be displayed at the top of the form.
	 * 
	 * @access	public
	 * 
	 */
	var $message;

	/**
	 * A way to pass extra parameters to the HTML form tag, for
	 * example 'enctype="multipart/formdata"'.  Notice: This property is
	 * deprecated in favour of the $_attrs list and the attr() and unset()
	 * methods.
	 * 
	 * @access	public
	 * 
	 */
	var $extra;

	/**
	 * Contains the message from any internal errors.
	 * 
	 * @access	public
	 * 
	 */
	var $error_message;

	/**
	 * Determines the way in which error messages are displayed.
	 * The default ('single') is to display the error message for the first
	 * invalid field only. Another ('all') is to display a list of all
	 * invalid fields with their corresponding error messages.  Please note
	 * that $error_mode 'all' assumes that a custom error message is provided
	 * for every rule. The other ('nolist') is to display no list at all, but only
     	 * a message "the fields were not filled in correctly".
	 *
	 * @access	public
	 * 
	 */
	var $error_mode = 'single';

	/**
	 * A list of all invalid fields in the form, and their corresponding
	 * error messages.
	 * 
	 * @access	public
	 * 
	 */
	var $invalid = array ();

	/**
	 * Contains a true or false value as to whether the form has been
	 * submitted successfully or not.  An invalid form will contain false.
	 * This value is used internally by the setValues() method.
	 * 
	 * @access	private
	 * 
	 */
	var $submitted = false;

	/**
	 * The name of this form.  Optional.  $name is actually an alias
	 * for $_attrs['name'].
	 * 
	 * @access	public
	 * 
	 */
	var $name;

	/**
	 * This contains a list of attributes of the HTML form tag.
	 * 
	 * @access	private
	 * 
	 */
	var $_attrs = array ();

	/**
	 * The email address to send the form to in the default handler.
	 * 
	 * @access	public
	 * 
	 */
	var $sendTo;

	/**
	 * The email address to send the form from in the default handler.
	 * 
	 * @access	public
	 * 
	 */
	var $sendFrom = '';

	/**
	 * Any extra parameters for the mail() function in the default handler.
	 * 
	 * @access	public
	 * 
	 */
	var $sendExtra = '';

	/**
	 * The subject line of the email to send from the default handler.
	 * 
	 * @access	public
	 * 
	 */
	var $sendSubject = 'Mail Form';

	/**
	 * The response to return upon a successfully submitted form in the
	 * default handler.  Defaults to "Thank you.  Your form has been sent."
	 * 
	 * @access	public
	 * 
	 */
	var $screenReply = 'Thank you.  Your form has been sent.';

	/**
	 * The function or object method to use to handle the submitted form.
	 * This function or method will be called by the run() method.  Use the
	 * setHandler() method to change this setting.
	 * 
	 * @access	public
	 * 
	 */
	var $handler;

	/**
	 * Whether to upload files automatically or to leave them for a custom
	 * saving mechanism.
	 *
	 * @access	public
	 *
	 */
	var $uploadFiles = true;

	/**
	 * Whether to verify the REQUEST_METHOD and HTTP_REFERER headers to make
	 * it more difficult (although not impossible) for spammers to abuse your
	 * form.  Note that this can be enabled in a form's settings file under
	 * the [Form] block via: verify_sender = yes
	 *
	 * @access	public
	 *
	 */
	var $verify_sender = false;

	/**
	 * Whether to strip all HTML and PHP tags/code from all input parameters.
	 * This is off by default because it would break forms with the Xed editor
	 * by default, so it must be enabled as needed.  Note that this can be
	 * enabled in a form's settings file under the [Form] block via:
	 * clean_input = yes
	 *
	 * @access	public
	 *
	 */
	var $clean_input = false;

	/**
	 * Whether to verify the remote address of the form submitter against
	 * a list of invalid IP addresses in the database table
	 * sitellite_form_blacklist so as to prevent repeated abuse from a single
	 * source.
	 *
	 * @access	public
	 *
	 */
	var $blacklist = true;

	/**
	 * Whether to verify that the submitter can accept session data, which
	 * helps ensure they are a legitimate human user.  Passing session
	 * verification requires cookies to be enabled for the submitter, which
	 * may help prevent abuse in combination with the other abuse-prevention
	 * techniques because a spambot may ignore the cookie, however this
	 * restricts forms for legitimate visitors who have cookies disabled
	 * in their browser (only a very small number of users disable cookies,
	 * but some do).  To disable for a single form, add verify_session = no
	 * to its settings.php form.
	 *
	 * @access	public
	 *
	 */
	var $verify_session = true;

	/**
	 * Whether this form should tie into Sitellite's autosave capabilities.
	 * Please note that the autosave handler is only available to authenticated
	 * users and not to anonymous forms.
	 *
	 * @access	public
	 *
	 */
	var $autosave = false;

	/**
	 * The context of the form. If this is set to 'action' then the title of
	 * the form will be set by calling page_title() instead of outputting an
	 * <h1> tag.
	 */
	var $context = false;

	/**
	 * Constructor Method.  Action will be set to $PHP_SELF if it
	 * is empty, unless a global $site object is defined in which case the
	 * action with be $site->url . $PHP_SELF.
	 * 
	 * @access	public
	 * @param	string	$action
	 * @param	string	$method
	 * 
	 */
	function MailForm ($action = '', $method = 'post') {
		$this->method = $method;
		if (empty ($action)) {
			if (function_exists ('site_current')) {
				$action = site_current ();
			} else {
				global $_SERVER;
				$action = $_SERVER['PHP_SELF'];
			}
			/*if (is_object ($GLOBALS['site'])) {
				global $site;
				$action = $site->url . $action;
			}*/
		}
		$this->action = $action;
		$GLOBALS['loader']->import ('saf.MailForm.Widget');

		// set up the $_attrs list
		$this->_attrs['action'] =& $this->action;
		$this->_attrs['method'] =& $this->method;
		$this->name = false;
		$this->_attrs['name'] =& $this->name;

		$this->handler = array (&$this, 'onSubmit');

		// attempt to automatically parse the settings.php file
		$cls = strtolower (get_class ($this));
		if ($cls != 'mailform') {
			if (function_exists ('site_current')) {
				$this->action = site_current ();
			} else {
				$this->action = $_SERVER['PHP_SELF'];
			}
			$this->_attrs['action'] =& $this->action;
			$app = loader_app ();
			if (strpos ($action, ':') === 1) {
				$action = str_replace ('\\', '/', $action);
			}
			list ($misc, $path) = explode ($app . '/forms/', $action);
			$box = dirname ($path);
			if (@file_exists ('inc/app/' . $app . '/forms/' . $box . '/settings.php')) {
				$this->parseSettings ('inc/app/' . $app . '/forms/' . $box . '/settings.php');
			}
		}
	}

	/**
	 * Adds another widget to the form.  If the $type is specified as
	 * a loader path, it will import from the proper location outside of
	 * saf.MailForm.Widget.*, and if you send an object as the $type value
	 * addWidget() will use that object as the widget (so make sure it is one!),
	 * as of version 3.8.
	 * 
	 * @access	public
	 * @param	string	$type
	 * @param	string	$name
	 * @return	object reference
	 * 
	 */
	function &addWidget ($type, $name) {
		if (is_object ($type)) {
			$this->widgets[$name] =& $type;
			$this->widgets[$name]->form =& $this;
			return $this->widgets[$name];
		} elseif (strpos ($type, '.') !== false) {
			loader_import ($type);
			$pieces = explode ('.', $type);
			$cls = 'MF_Widget_' . strtolower (array_pop ($pieces));
		} else {
			$cls = 'MF_Widget_' . $type;
			loader_import ('saf.MailForm.Widget.' . ucfirst ($type));
		}
		$this->widgets[$name] = new $cls ($name);
		$this->widgets[$name]->form =& $this;
		return $this->widgets[$name];
	}

	/**
	 * Validates the form values against a global $cgi object.  Used in
	 * the logic of "if the form is invalid then...".  Also sets an internal
	 * $invalid_field property.  Returns true if the form is invalid or has not
	 * been filled out yet.  If the form passes (false returned), it also sets
	 * the $submitted value to true.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function invalid () {
		global $cgi;

		$this->invalid = array ();

		$this->submitted = false;

		// determine if form has been submitted (based on submit buttons only)
		// making sure form still submits if there are more than one submit button
		$ret = true;
		$tpl = array ();
		foreach ($this->widgets as $k => $v) {
			if (in_array (strtolower (get_class ($v)), array ('mf_widget_submit', 'mf_widget_msubmit')) && isset ($cgi->{$k})) {
				$ret = false;
				break;
			} elseif (strtolower (get_class ($v)) == 'mf_widget_image' && isset ($cgi->{$k . '_x'})) {
				$ret = false;
				break;
			} elseif (strtolower (get_class ($v)) == 'mf_widget_template' && count ($v->submitButtons) > 0) {
				$tpl[$k] = $v->submitButtons;
			}
		}
		if ($ret) {
			foreach ($tpl as $k => $buttons) {
				foreach ($buttons as $button) {
					if (isset ($cgi->{$button})) {
						$ret = false;
						break;
					}
				}
			}
			
			if ($ret) {
				// form not yet submitted
				return true;
			}
		}

		$ret = false;
		foreach (array_keys ($this->widgets) as $key) {
			$widget =& $this->widgets[$key];

			// must compile value of widget here, so compound widgets can validate
			$widget_value = $widget->getValue ($cgi);

			if (in_array (strtolower (get_class ($widget)), array ('mf_widget_submit', 'mf_widget_msubmit', 'mf_widget_image'))) {
				// we've already checked submit buttons
				continue;

			} elseif (! $widget->passover_isset && ! isset ($cgi->{$key})) {
				// specified field is missing, assuming form not yet submitted
				return true;
			}
			//if ($key == 'password') {
			//	echo 'aha!';
			//}

			if (! $widget->validate ($widget_value, $this, $cgi)) {
				if (! $ret) {
					$this->invalid_field = $widget->name;
					$ret = true;
				}
				$this->invalid[$widget->name] = $widget->error_message;
			}
		}
		$this->submitted = true;
		if ($ret) {
			return true;
		}

		if ($this->verify_sender) {
			if (! $this->verifyRequestMethod ()) {
				die ('Invalid request method!');
			}
			if (! $this->verifyReferer ()) {
				die ('Invalid referrer!');
			}
		}

		return false;
	}

	/**
	 * Manually set a specific field to be invalid, including a custom error message.
	 * Useful for setting an error message during the onSubmit() before calling
	 * return $this->show(); again.
	 *
	 * @access	public
	 * @param	string	$field
	 * @param	string	$message
	 *
	 */
	function setInvalid ($field, $message) {
		$this->invalid_field = $field;
		$this->invalid[$field] = $message;
		$this->widgets[$field]->error_message = $message;
	}

	/**
	 * Sets the values of the form widgets from a provided CGI object.
	 * The second parameter is deprecated.
	 * 
	 * @access	public
	 * @param	object	$cgi
	 * @param	boolean	$invalid
	 * 
	 */
	function setValues ($cgi, $invalid = false) {
		foreach ($cgi->param as $key) {
			$value = $cgi->{$key};
			if (is_object ($this->widgets[$key])) {
				$this->widgets[$key]->setValue ($value);
			} elseif (preg_match ('/^(MF_)+([a-zA-Z0-9-]+)_([A-Z0-9_]+)$/', $key, $regs)) {
				//echo '<pre>'; print_r ($regs); echo '</pre>';
				if (is_object ($this->widgets[$regs[2]])) {
					$this->widgets[$regs[2]]->setValue ($value, $regs[3]);
				}
			}
		}

		// loop through widgets who are not set but who have passover_isset set to true
		// and set their values to ''.
		if ($this->submitted) {
			foreach ($this->widgets as $key => $widget) {
				if ($widget->passover_isset && ! isset ($cgi->{$key})) {
					$this->widgets[$key]->setValue ('');
				}
			}
		}
		$this->cgi = $cgi;
	}

	/**
	 * Generates the HTML form.  You can provide an optional template
	 * to customize the look of the form.  Template directives (ie. ##field##)
	 * must be provided for each form widget, as well as ##mailform_action## and
	 * ##mailform_method##, which correspond to the action and method attributes
	 * of the HTML form tag.
	 * 
	 * @access	public
	 * @param	string	$template
	 * @return	string
	 * 
	 */
	function show ($template = '') {
		/*if (session_pref ('browse_level') == 'normal') {
			foreach ($this->widgets as $key => $widget) {
				if ($widget->advanced) {
					$this->widgets[$key] =& $this->widgets[$key]->changeType ('hidden');
				}
			}
		}*/

		if (! empty ($template)) {
			// last minute template setting
			$this->template = $template;
		}
		if ($this->formhelp) {
			page_add_script (site_prefix () . '/js/formhelp-compressed.js');
		}

		if ($this->autosave) {
			global $cgi;
			loader_import ('saf.MailForm.Autosave');
			$a = new Autosave ();
			if ($a->has_draft ()) {
				if ($cgi->_autosave_start_from == intl_get ('Restore your previous editing session')) {
					$data = $a->retrieve ();
					foreach ($data as $k => $v) {
						if (is_object ($this->widgets[$k])) {
							$this->widgets[$k]->setValue ($v);
						}
						$cgi->{$k} = $v;
						$cgi->param[] = $k;
					}
				} elseif ($cgi->_autosave_start_from == intl_get ('Start from the currently saved version')) {
					$a->clear ();
				} else {
					// has an existing autosave version, prompt for action
					return '<p>' . intl_get ('An auto-saved edit of this form has been found from a previous editing session.  Would you like to continue from your previous editing session, or start from the currently saved version?') . '</p>'
						. '<form method="post" action="' . $_SERVER['REQUEST_URI'] . '">'
						. '<p><input type="submit" name="_autosave_start_from" value="' . intl_get ('Restore your previous editing session') . '" /> &nbsp; &nbsp; '
						. '<input type="submit" name="_autosave_start_from" value="' . intl_get ('Start from the currently saved version') . '" /></p>'
						. '</form>';
				}
			}
			page_add_script (site_prefix () . '/js/rpc.js');
			page_add_script (site_prefix () . '/js/autosave.js');
		}

		if (! empty ($this->template)) {
			// use template to display form

			$form_contents = array ();

			// determine appropriate instructional message
			if (! empty ($this->invalid_field)) {
				if ($this->error_mode == 'single') {
					// display only the first error message (the default)
					if (! empty ($this->widgets[$this->invalid_field]->error_message)) {
						$form_contents['mailform_message'] = $this->widgets[$this->invalid_field]->error_message;
					} else {
						$form_contents['mailform_message'] = intl_get ('Oops! The following field was not filled in correctly:') . ' ' .
							$this->widgets[$this->invalid_field]->display_value .
							'. ' . intl_get ('Please fix this before continuing.');
					}
					$this->widgets[$this->invalid_field]->invalid = true;
// Start: SEMIAS #171 nolist error_mode.
				} elseif ($this->error_mode == 'nolist') {
                    // do not display error messages
					$form_contents['mailform_message'] = intl_get ('The following fields were not filled in correctly') . ".";
                    foreach ($this->invalid as $name => $message) {
						// $_message .= TAB . '<li>' . $message . '</li>' . NEWLINE;
						$this->widgets[$name]->invalid = true;
					}
// END: SEMIAS
                } else {
					// display all error messages
					$form_contents['mailform_message'] = intl_get ('Oops!  The following information must be corrected in order to continue:') . NEWLINE;
					$form_contents['mailform_message'] .= '<ul>' . NEWLINE;
					foreach ($this->invalid as $name => $message) {
						$form_contents['mailform_message'] .= TAB . '<li>' . $message . '</li>' . NEWLINE;
						$this->widgets[$name]->invalid = true;
					}
					$form_contents['mailform_message'] .= '</ul>' . NEWLINE;
				}
			} else {
				$form_contents['mailform_message'] = $this->message;
			}

			foreach ($this->widgets as $key =>$widget) {
				//$form_contents[$key] = str_replace ('##', 'SITELLITE_DOUBLE_POUND_SUBSTITUTION', $widget->display (0));
				if (! is_object ($this->widgets[$key]->form)) {
					$this->widgets[$key]->form =& $this;
				}
				$form_contents[$key] = $this->widgets[$key]->display (0);
			}

			$form_contents['mailform_title'] = $this->title;
			$form_contents['mailform_method'] = $this->method;
			$form_contents['mailform_action'] = $this->action;
			$form_contents['mailform_extra'] = $this->extra;
			$form_contents['mailform_attrs'] = $this->getAttrs ();

			global $simple;
			//$loader->import ('saf.Template.Simple');
			//$tpl = new SimpleTemplate ('', SIMPLE_TEMPLATE_DELIM_POUND);
			//$simple = new SimpleTemplate ('');
			$return_data = $simple->fill ($this->template, $form_contents);
			//return str_replace ('SITELLITE_DOUBLE_POUND_SUBSTITUTION', '##', $return_data);
			return $return_data;

		} else {

			// determine appropriate instructional message
			$_message = '';
			if (! empty ($this->invalid_field)) {
				if ($this->error_mode == 'single') {
					if (! empty ($this->widgets[$this->invalid_field]->error_message)) {
						$_message .= '<p class="invalid">' . $this->widgets[$this->invalid_field]->error_message . "</p>\n";
					} else {
						$_message .= '<p class="invalid">' . intl_get ('Oops! The following field was not filled in correctly:') . ' ' .
							$this->widgets[$this->invalid_field]->display_value .
							'. ' . intl_get ('Please fix this before continuing.') . "</p>\n";
					}
					$this->widgets[$this->invalid_field]->invalid = true;
// Start: SEMIAS #171 nolist error_mode.
				} elseif ($this->error_mode == 'nolist') {
				    // display no error messages at all
                    $_message .= '<p class="invalid">' . intl_get ('The fields marked below were not filled in correctly') . ".</p>\n" ;
                    foreach ($this->invalid as $name => $message) {
						$this->widgets[$name]->invalid = true;
					}
// END: SEMIAS
				} else {
					// display all error messages
					$_message .= '<p class="invalid">' . intl_get ('Oops!  The following information must be corrected in order to continue:') . NEWLINE;
					$_message .= '<ul>' . NEWLINE;
					foreach ($this->invalid as $name => $message) {
						$_message .= TAB . '<li>' . $message . '</li>' . NEWLINE;
						$this->widgets[$name]->invalid = true;
					}
					$_message .= '</ul>' . NEWLINE . '</p>' . NEWLINEx2;
				}
			} elseif (! empty ($this->message)) {
				$_message .= '<p>' . $this->message . "</p>\n";
			}

			$_widgets = '';
			foreach ($this->widgets as $key => $widget) {
				if (! method_exists ($this->widgets[$key], 'display')) {
					die ('Widget "' . $key . '" has no display() method!');
				}
				if (! is_object ($this->widgets[$key]->form)) {
					$this->widgets[$key]->form =& $this;
				}
				$_widgets .= $this->widgets[$key]->display (1);
			}

			$attrstr = $this->getAttrs ();
			if (! empty ($this->name)) {
				$data =  '<form ' . $attrstr . ' ' . $this->extra . '>' . "\n";
			} else {
				$data =  '<form ' . $attrstr . ' ' . $this->extra . '>' . "\n";
			}

			if (! empty ($this->title)) {
				if ($this->context == 'action') {
					page_title ($this->title);
				} else {
					$data .= '<h1>' . $this->title . '</h1>';
				}
			}

			$data .= $_message;
			$align = empty ($this->align) ? 'center' : $this->align;

			//$data .= '<table border="0" cellspacing="0" cellpadding="0" align="' . $align . '">' . "\n";
            		$data .= '<table border="0" cellspacing="0" cellpadding="0">' . "\n";
			$data .= $_widgets;
			return $data . '</table>' . "\n" . '</form>';
		}
	}

	/**
	 * Returns the form values as an associative array.  If $uploadFiles
	 * is set to true, it will return the saved path or false for file widgets,
	 * otherwise it will return the saf.CGI.UploadedFile object and not act
	 * on the object for you.
	 * 
	 * @access	public
	 * @param	boolean	$uploadFiles
	 * @return	associative array
	 * 
	 */
	function getValues () {
		global $cgi;

		$uploadFiles = $this->uploadFiles;

		$return = array ();
		foreach ($this->widgets as $key => $obj) {
			if ($uploadFiles && strtolower (get_class ($obj)) == 'mf_widget_file') {
				$return[$key] = $obj->move ();
			} else if ($uploadFiles && strtolower (get_class ($obj)) == 'mf_widget_mfile') {
				$return[$key] = $obj->move ();
			} else {
				if (is_object ($cgi)) {
					$return[$key] = $obj->getValue ($cgi);
				} else {
					$return[$key] = $obj->getValue ();
				}
			}
		}
		return $return;
	}

	/**
	 * Sets the function to use to handle the output of the current form.
	 * To specify a method of an object, pass it an array with an object reference
	 * as the first element and the method name as the second.  The default handler
	 * is the internal onSubmit().  The handler is called using call_user_func()
	 * in the run() method.
	 * 
	 * @access	public
	 * @param	mixed	$func
	 * 
	 */
	function setHandler ($func) {
		$this->handler = $func;
	}

	/**
	 * Runs the form and returns either the rendered form or the output
	 * of the handler function.  $uploadFiles can be set to false to cause the
	 * getValues() method not to call move() on File widgets.  This is useful
	 * for situations when you need to do something other than simply save the
	 * file to a predetermined folder.  Please note: The $uploadFiles parameter
	 * is deprecated in favour of the $uploadFiles property of the MailForm
	 * class.  This allows the setting to be managed via a settings.php file.
	 * 
	 * @access	public
	 * @param	boolean	$uploadFiles
	 * @return	string
	 * 
	 */
	function run ($uploadFiles = true) {
		global $cgi;

		if (! $uploadFiles) {
			$this->uploadFiles = $uploadFiles;
		}

		if ($this->invalid ($cgi)) {
			$this->setValues ($cgi);

			if ($this->verify_session) {
				@session_start ();
				$_SESSION['mf_verify_session'] = 'mf_verified';
			}

			return $this->show ();
		} else {
			if ($this->verify_session) {
				@session_start ();
				if ($_SESSION['mf_verify_session'] != 'mf_verified') {
					die ('This form requires that you enable cookies in your browser, which helps us to prevent abuse of our forms by automated spam systems.');
				}
			}
			if ($this->blacklist) {
				if (db_shift ('select count(*) from sitellite_form_blacklist where ip_address = ?', $_SERVER['REMOTE_ADDR'])) {
					die ('The IP address submitting this form has been blacklisted due to abuse.  If you feel this has been done in error, please contact the website owner.');
				}
			}

			if ($this->autosave) {
				loader_import ('saf.MailForm.Autosave');
				$a = new Autosave ();
				$a->clear ($_SERVER['HTTP_REFERER']);
			}

			$this->setValues ($cgi);
			$vals = $this->getValues ();
			if ($this->clean_input) {
				foreach ($vals as $k => $v) {
					$vals[$k] = strip_tags ($v);
				}
			}
			return call_user_func ($this->handler, $vals);
		}
	}

	/**
	 * This is the default handler function.  It is called via run()
	 * and can be overridden via subclassing.
	 * 
	 * @access	public
	 * @param	array	$vals
	 * @return	string
	 * 
	 */
	function onSubmit ($vals) {
		if (! empty ($this->sendTo) || ! empty ($this->email)) {
			$email = $this->sendTo;
			$subject = $this->sendSubject;
			$from_field = $this->sendFrom;
			$extra = $this->sendExtra;

			global $site;

			$message_body = intl_get ('The following information has been sent from') . ' ' . $site->url . $GLOBALS['PHP_SELF'] . ":\n\n";
			foreach ($vals as $key => $value) {
				if ($this->widgets[$key]->type == 'separator') {
					$message_body .= "\n----------\n\n";
				} elseif ($this->widgets[$key]->type == 'section') {
					$message_body .= $this->widgets[$key]->title . "\n\n";
				} else {
					if (strlen ($this->widgets[$key]->alt) > 30) {
						$alt = substr ($this->widgets[$key]->alt, 0, 27) . '...';
					} else {
						$alt = $this->widgets[$key]->alt;
					}
					$message_body .= str_pad ($alt, 35) . ": " . $value . "\n";
				}
			}
			if (! empty ($from_field)) {
				if (strpos ($vars[$from_field], "\n") !== false) {
					die ('Invalid from field value.');
				}
				$from_field = 'From: ' . $vals[$from_field] . "\r\n";
			}

			//echo '<pre>' . htmlentities_compat ($message_body) . '</pre>';

			if (@mail ($email, $subject, $message_body, $from_field . $extra)) {
				return $this->screenReply;
			} else {
				$this->error_message = intl_get ('Email could not sent.  Please verify that your mail daemon is functioning correctly.');
				return $this->error_message;
			}
		}
		$this->error_message = intl_get ('No email address set.  Please check your form settings.');
		return $this->error_message;
	}

	/**
	 * Note: Deprecated in favour of the setHandler() and run() methods
	 * and subclassing. Handles the form, once it has been satisfactorily completed.
	 * If the first parameter points to a file (ie. 'inc/forms/contact.php'), it will
	 * use that file as an "action file" to handle the form.  Otherwise, the first
	 * parameter must be an email address, as handle () will simply send an email
	 * of the form contents to the specified email address.  Note: Extra will be
	 * passed to the PHP mail () function as a fourth parameter, or can be used for
	 * any purpose you'd like in an action file.
	 * 
	 * @access	public
	 * @param	string	$email
	 * @param	string	$subject
	 * @param	string	$from_field
	 * @param	string	$extra
	 * @return	boolean
	 * 
	 */
	function handle ($email, $subject = 'Mail Form', $from_field = '', $extra = '') {
		if (! strstr ($email, '@')) {
			//echo 'using actionfile: ' . $email . '<br />';
			// $email is an action file, so give it some action! ;)
			if (strstr ($email, '..')) {
				// we don't like file paths with .. in them.  they really should never be needed.
				$this->error_message = intl_get ('Sorry, no action file names with ".." in them.');
				return false;
			} elseif (! @file_exists ($email)) {
				$this->error_message = intl_get ('Sorry, the action file you have specified could not be found.');
				return false;
			} else {
				include_once ($email);
				return true;
			}
		} else {
			// send email
			//echo 'using email address: ' . $email . '<br />';
			$vars = $this->getValues ();
			global $site;
			$message_body = $this->formatEmail ($vars);
			if (! empty ($from_field)) {
				if (strpos ($vars[$from_field], "\n") !== false) {
					die ('Invalid from field value.');
				}
				$from_field = 'From: ' . $vars[$from_field] . "\r\n";
			}

			//echo '<pre>' . htmlentities_compat ($message_body) . '</pre>';

			if (@mail ($email, $subject, $message_body, $from_field . $extra)) {
				return true;
			} else {
				$this->error_message = intl_get ('Email could not sent.  Please verify that your mail daemon is functioning correctly.');
				return false;
			}

			//echo "<pre>mail ('$email', '$subject', '$message_body', '$from_field$extra')</pre>";
			//return true;
		}
	}

	function formatEmail ($vals) {
		global $site;

		$message_body = intl_get ('The following information has been sent from') . ' ' . $site->url . $GLOBALS['PHP_SELF'] . ":\n\n";
		foreach ($vals as $key => $value) {
			if ($this->widgets[$key]->type == 'separator') {
				$message_body .= "\n----------\n\n";
			} elseif ($this->widgets[$key]->type == 'section') {
				$message_body .= $this->widgets[$key]->title . "\n\n";
			} else {
				if (strlen ($this->widgets[$key]->alt) > 30) {
					$alt = substr ($this->widgets[$key]->alt, 0, 27) . '...';
				} else {
					$alt = $this->widgets[$key]->alt;
				}
				$message_body .= str_pad ($alt, 35) . ": " . $value . "\n";
			}
		}
		return $message_body;
	}

	/**
	 * Parses the specified file using the parse_ini_file()
	 * function.  Sections in the file correspond to the names of
	 * widgets you wish to create, in addition to a [Form] section
	 * that sets properties for the form itself.  The values in
	 * each section correspond to properties or methods of the
	 * widgets.  This method can be used to simplify the process
	 * of defining and customizing a form.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @return	boolean
	 * 
	 */
	function parseSettings ($file) {
		if (! @file_exists ($file)) {
			return false;
		}

		ini_add_filter ('ini_filter_split_comma_single', array (
			'rule 0', 'rule 1', 'rule 2', 'rule 3', 'rule 4', 'rule 5', 'rule 6', 'rule 7', 'rule 8',
			'button 0', 'button 1', 'button 2', 'button 3', 'button 4', 'button 5', 'button 6', 'button 7', 'button 8',
			'submitButtons',
		));

		$conf = ini_parse ($file, true);

		ini_clear ();

		if (count ($conf) == 0) {
			return false;
		}

		// form properties, optional
		if (is_array ($conf['Form'])) {
			foreach ($conf['Form'] as $key => $value) {
				if (($key == 'title' || $key == 'message') && function_exists ('intl_get')) {
					$value = intl_get ($value);
				}
				$this->{$key} = $value;
			}
			unset ($conf['Form']);
		}

		foreach ($conf as $name => $data) {
			$this->createWidget ($name, $data);
		}

		return true;
	}

	/**
	 * Creates a widget from a name and an array, usually taken from a parsed
	 * settings.php (ini formatted) file.
	 *
	 * @access	public
	 * @param	string
	 * @param	array hash
	 */
	function &createWidget ($name, $data) {// create widget
		$type = $data['type'];
		unset ($data['type']);
		$widget =& $this->addWidget ($type, $name);

		// handle setValues
		if (! empty ($data['setValues'])) {
			if (strpos ($data['setValues'], 'eval:') === 0) {
				eval (CLOSE_TAG . OPEN_TAG . ' $widget->setValues (' . substr ($data['setValues'], 5) . '); ' . CLOSE_TAG);
			} elseif (preg_match ('/, ?/', $data['setValues'])) {
				$widget->setValues (preg_split ('/, ?/', $data['setValues']));
//				eval (CLOSE_TAG . OPEN_TAG . ' $widget->setValues (array (' . $data['setValues'] . ')); ' . CLOSE_TAG);
			} else {
				if (strpos ($data['setValues'], '\'') === 0 && strrpos ($data['setValues'], '\'') == strlen ($data['setValues']) - 1) {
					$data['setValues'] = substr ($data['setValues'], 1, strlen ($data['setValues']) - 2);
				}
				$widget->setValues ($data['setValues']);
			}
//			eval (CLOSE_TAG . OPEN_TAG . ' $widget->setValues (' . $data['setValues'] . '); ' . CLOSE_TAG);
		}
		unset ($data['setValues']);

		if (! empty ($data['setDefault'])) {
			if (strpos ($data['setDefault'], 'eval:') === 0) {
				eval (CLOSE_TAG . OPEN_TAG . ' $widget->setDefault (' . substr ($data['setDefault'], 5) . '); ' . CLOSE_TAG);
			} else {
				$widget->setDefault ($data['setDefault']);
			}
		}

		if (! empty ($data['setValue'])) {
			if (strpos ($data['setValue'], 'eval:') === 0) {
				eval (CLOSE_TAG . OPEN_TAG . ' $widget->setValue (' . substr ($data['setValue'], 5) . '); ' . CLOSE_TAG);
			} else {
				$widget->setValue ($data['setValue']);
			}
		}

		if (! empty ($data['setRules'])) {
			if (strpos ($data['setRules'], 'eval:') === 0) {
				eval (CLOSE_TAG . OPEN_TAG . ' $widget->rules = ' . substr ($data['setRules'], 5) . '); ' . CLOSE_TAG);
			}
		}

		// handle rules
		//foreach (preg_split ('/, ?/', $data['rules'], -1, PREG_SPLIT_NO_EMPTY) as $rule) {
		//	$widget->addRule ($rule);
		//}
		//unset ($data['rules']);

		// widget properties
		foreach ($data as $key => $value) {
			if (strpos ($key, 'rule ') === 0) {
				if (is_array ($value)) {
					if (function_exists ('intl_get')) {
						$value[1] = intl_get ($value[1]);
					}
					$widget->addRule ($value[0], $value[1]);
				} else {
					$widget->addRule ($value);
				}
			} elseif (strpos ($key, 'value ') === 0) {
				$widget->setValues ($value, $value);
			} elseif ($type == 'msubmit' && strpos ($key, 'button ') === 0) {
				$b =& $widget->getButton ();
				if (empty ($b->value)) {
					if (is_array ($value)) {
						if (function_exists ('intl_get')) {
							$value[0] = intl_get ($value[0]);
						}
						$b->setValues ($value[0]);
						$b->extra = $value[1];
					} else {
						if (function_exists ('intl_get')) {
							$value = intl_get ($value);
						}
						$b->setValues ($value);
					}
				} else {
					if (is_array ($value)) {
						if (function_exists ('intl_get')) {
							$value[0] = intl_get ($value[0]);
						}
						$b =& $widget->addButton ($name, $value[0]);
						$b->extra = $value[1];
					} else {
						if (function_exists ('intl_get')) {
							$value = intl_get ($value);
						}
						$widget->addButton ($name, $value);
					}
				}
			} elseif ($key == 'alt') {
				if (function_exists ('intl_get')) {
					$widget->{$key} = intl_get ($value);
				} else {
					$widget->{$key} = $value;
				}
			} elseif (method_exists ($widget, $key)) {
				$widget->{$key} ($value);
			} else {
				$widget->{$key} = $value;
			}
		}

		if (! empty ($widget->formhelp) && session_pref ('form_help') == 'on') {
			$widget->extra .= ' onfocus="formhelp_show (this, \'' . addslashes (intl_get ($widget->formhelp)) . '\')" onblur="formhelp_hide ()"';
		}

		unset ($widget);
		return $this->widgets[$name];
	}

	/**
	 * Takes a non-associative array and creates an associative array
	 * out of its values.  This is used to send non-associative arrays to the
	 * setValues() method of the Widget objects.
	 * 
	 * @access	public
	 * @param	array	$list
	 * @return	associative array
	 * 
	 */
	function makeAssoc ($list) {
		$new = array ();
		foreach ($list as $key => $value) {
			if (! is_int ($key)) {
				$new[$key] = $value;
			} else {
				$new[$value] = $value;
			}
		}
		return $new;
	}

	/**
	 * Takes a non-associative array listing the names of each field
	 * from $cgi you want to "remember", and creates hidden fields for each
	 * of them, so you don't have to hard-code lists of hidden fields in
	 * multi-screen forms.
	 * 
	 * @access	public
	 * @param	array	$list
	 * 
	 */
	function rememberFields ($list) {
		foreach ($list as $field) {
			$this->addWidget ('hidden', $field);
		}
	}

	/**
	 * This is the accessor method for setting and getting the value of
	 * any attribute of the form tag, including 'method' and 'action'.  This will
	 * replace the $extra property, which is henceforth deprecated.  If you call
	 * this method and provide no $value, you are using it as a 'getter', as in
	 * you are getting the current value.  If you provide a value, the new value
	 * will be set, so you are acting as a 'setter'.  If you simply specify that
	 * the $value be true, then it will appear filled with its own name (useful
	 * for things like the checked="checked" attribute of a checkbox input field,
	 * even though this isn't a checkbox field).
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
	 * Use this method to remove an attribute from the form tag
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
	 * Returns a list of all of the attributes of this object's form tag
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

	function verifyRequestMethod () {
		if (strtoupper ($_SERVER['REQUEST_METHOD']) != strtoupper ($this->method)) {
			return false;
		}
		return true;
	}

	function verifyReferer () {
		if (strpos ($_SERVER['HTTP_REFERER'], site_url ()) !== 0) {
			return false;
		}
		return true;
	}
}

?>
