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
// Security widget.  Performs a CAPTCHA test on the user.
//

/**
	 * Security widget.  Performs a CAPTCHA test on the user.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_security ('name');
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
	 * @version	1.2, 2002-05-03, $Id: Security.php,v 1.2 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_security extends MF_Widget {
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
	var $type = 'security';

	/**
	 * This is the method to use to verify the user is human.  The default
	 * is 'figlet' which renders random letters and numbers using a combination
	 * of ascii symbols on several lines.  This technique is less typical
	 * than other form security techniques, which may increase its security
	 * slightly, but it is also text-based and therefore less secure in that
	 * regard.  It is the default because it requires no special PHP extensions
	 * to use.
	 *
	 * The alternative is 'turing' which generates an image of random letters
	 * and numbers, making it a slightly more effective security precaution.
	 * This requires PHP's GD extension however, which is not available on all
	 * systems.  Check your phpinfo() output to determine compatibility.
	 *
	 * A third method is 'recaptcha' which uses the recaptcha.net service.
	 * This requires the global conf('Services','recaptcha_public_key') and
	 * conf('Services','recaptcha_private_key') settings to be set, in which
	 * case this method will be used automatically as the default.
	 *
	 * Turing tests are also known as CAPTCHA tests.  Their purpose is to
	 * verify that the user is human by having them perform a test that would
	 * be difficult for a computer to pass.
	 *
	 * @access	public
	 *
	 */
	var $verify_method = 'figlet';

//START: SEMIAS. #188 - form captcha improvements.
	/**
     * Image.php file for customized settings, see /saf/lib/ext/phpcaptcha/image.php
     *
     * @access  public
     *
     */
    var $phpcaptcha_image = '';
//END: SEMIAS.

    /**
	 * Public key from recaptcha.net
	 *
	 * @access	public
	 */
	var $recaptcha_public_key = false;

	/**
	 * Private key from recaptcha.net
	 *
	 * @access	public
	 */
	var $recaptcha_private_key = false;

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_security ($name) {
		parent::MF_Widget ($name);

		$key = conf ('Services', 'recaptcha_public_key');
		if (! empty ($key)) {
			$this->verify_method = 'recaptcha';
			$this->recaptcha_public_key = $key;
			$this->recaptcha_private_key = conf ('Services', 'recaptcha_private_key');
		}

		$this->addRule (
			'func "mailform_widget_security_verify"',
			intl_get ('Your input does not match the letters and numbers shown in the security field.  Please try again.')
		);
	}

	function verify () {
		global $cgi;
//START: SEMIAS. #188 - form captcha improvements.
        if ($this->verify_method == 'phpcaptcha') {
            $code = trim ($cgi->{$this->name . '_hash'} );
            $to_check = md5 ($code);

            if($to_check == $_SESSION['security_code']) {
                return true;
            } else {
                return false;
            }

        }
		elseif ($this->verify_method == 'turing') {
//END: SEMIAS.
			loader_import ('saf.Security.Turing');
			if (! SECURITY_TURING_GD_LOADED) {
				die ('Your server does not have GD support, which is necessary to render the turing test for this form.');
			}
			$sec = new Security_Turing ();
		} elseif ($this->verify_method == 'recaptcha') {
			loader_import ('ext.recaptcha');
			$res = recaptcha_check_answer (
				conf ('Other', 'recaptcha_private_key'),
				$_SERVER['REMOTE_ADDR'],
				$_POST['recaptcha_challenge_field'],
				$_POST['recaptcha_response_field']
			);
			return $res->is_valid;
		} else {
			loader_import ('saf.Security.Figlet');
			$sec = new Security_Figlet ();
		}
		return $sec->verify ($cgi->{$this->name}, $cgi->{$this->name . '_hash'});
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
		parent::display ($generate_html);
		global $intl, $simple;
		$attrstr = $this->getAttrs ();
//START: SEMIAS. #188 - form captcha improvements.
        if ($this->verify_method == 'phpcaptcha') {

            if(!empty ($this->phpcaptcha_image)) {
                $image = $this->phpcaptcha_image;
            } else {
                $image = "/saf/lib/Ext/phpcaptcha/image.php";
            }

            page_add_script("
                function new_captcha()
                {
                var c_currentTime = new Date();
                var c_miliseconds = c_currentTime.getTime();

                document.getElementById('captcha').src = ' " . $image . "?x='+ c_miliseconds;
                }
            ");

            $html = '<img border="0" id="captcha" src="' . $image . '" alt="">
                     &nbsp;<a href="JavaScript: new_captcha();"><img id="captcha-refresh" border="0" alt="" src="/saf/lib/Ext/phpcaptcha/refresh.png"></a>';

			return sprintf (
				"\t<tr>\n\t\t<td class='label' colspan='2'><label for='%s' id='%s-label'%s>%s</label></td></tr>
				<tr><td class='field-input' colspan='1'><input type='text' name='%s_hash' /></td><td class='field-captcha' colspan='1'>%s<input type='hidden' name='%s' id='%s' />
                </td></tr>\n<tr><td class='field-help' colspan='2'>" . intl_get('Enter the string shown in the image. (Case sensitive)') . "</td></tr>\n",
				$this->name,
				$this->name,
				$this->invalid (),
				$simple->fill ($this->label_template, $this, '', true),
                $this->name,
                $html,
				$this->name,
				$this->name
			);


        } elseif ($this->verify_method == 'turing') {
//END: SEMIAS.
			loader_import ('saf.Security.Turing');
			if (! TURING_TEST_GD_LOADED) {
				die ('Your server does not have GD support, which is necessary to render the turing test for this form.');
			}
			$sec = new Security_Turing ();
            list ($pre, $hash) = $sec->makeTest ();
		} elseif ($this->verify_method == 'recaptcha') {
			loader_import ('ext.recaptcha');
			$html = recaptcha_get_html (conf ('Other', 'recaptcha_public_key'));
			return sprintf (
				"\t<tr>\n\t\t<td class='label' colspan='2'><label for='%s' id='%s-label'%s>%s</label></td></tr>
				<tr><td class='field-input' colspan='1'><input type='hidden' name='%s_hash' /></td><td class='field-captcha' colspan='1'>%s<input type='hidden' name='%s' id='%s' />
                </td></tr>\n<tr><td class='field-help' colspan='2'>" . intl_get('Enter the string shown in the image. (Case sensitive)') . "</td></tr>\n",
				$this->name,
				$this->name,
				$this->invalid (),
				$simple->fill ($this->label_template, $this, '', true),
                $this->name,
                $html,
				$this->name,
				$this->name
			);
		} else {
			loader_import ('saf.Security.Figlet');
			$sec = new Security_Figlet ();
            list ($pre, $hash) = $sec->makeTest ();
		}

		if ($generate_html) {
			return "\t" . '<tr>' . "\n\t\t" . '<td class="label" colspan="2"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td></tr>' . "\n\t\t" .
				'<tr><td class="field-input" colspan="1"><input type="text" ' . $attrstr . ' " maxlength="6" size="20" style="margin-top: 5px" ' . $this->extra . ' /></td><td class="field-captcha" colspan="1">' . $pre .
                '<input type="hidden" name="' . $this->name . '_hash" value="' . $hash . '" /></td>' . "\n\t" . '</tr>' . "\n<tr><td class='field-help' colspan='2'>" . intl_get('Enter the string shown in the image. (Case sensitive)') . "</td></tr>\n";
		} else {
			return '<input type="text" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '" ' . $this->extra . ' />';
		}
	}
}

function mailform_widget_security_verify ($vals) {
	global $cgi, $mailform_current_form;
	foreach ($vals as $k => $v) {
		if (isset ($cgi->{$k . '_hash'})) {
			if (! $mailform_current_form->widgets[$k]->verify ()) {
				return false;
			}
		}
	}
	return true;
}

?>