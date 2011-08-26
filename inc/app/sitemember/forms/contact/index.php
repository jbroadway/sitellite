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
// resolved tickets:
// #198 Allow for HTML mailing templates.

$on = appconf ('contact');
if (! $on) {
	header ('Location: ' . site_prefix () . '/index/sitemember-app');
	exit;
} elseif ($on != 'form:sitemember/contact') {
	list ($type, $call) = split (':', $on);
	$func = 'loader_' . $type;
	echo $func (trim ($call), array (), $context);
	return;
}

class SitememberContactForm extends MailForm {
	function SitememberContactForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/sitemember/forms/contact/settings.php');

		global $cgi;

		if (! isset ($cgi->user)) {
			header ('Location: ' . site_prefix () . '/index/sitemember-app');
			exit;
		}

		$this->member = session_get_user ($cgi->user);
		if (! is_object ($this->member) || $this->member->public != 'yes') {
			header ('Location: ' . site_prefix () . '/index/sitemember-app');
			exit;
		}

		page_title (intl_get ('Member Contact Form') . ': ' . $cgi->user);

		if (session_valid ()) {
			$info = session_get_user ();
			$this->widgets['email']->setValue ($info->email);
		}
	}
//START: SEMIAS. #198 Allow for HTML mailing templates.
//-----------------------------------------------
/*
	function onSubmit ($vals) {
		if (! @mail ($this->member->email, $vals['subject'], $vals['message'], 'From: ' . $vals['email'])) {
			page_title ('Unknown Error');
			echo '<p>' . intl_get ('An error occurred trying to send the message.  Please try again later.') . '</p>';
			return;
		}

		page_title (intl_get ('Message Sent'));
		echo '<p>' . intl_get ('Your message has been sent.') . '</p>';
	}
}
*/
//-----------------------------------------------
    function onSubmit ($vals) {
		if (! site_mail (
                $this->member->email,
                $vals['subject'],
                $vals['message'],
                'From: ' . $vals['email'],
                array ("Is_HTML" => true)
            )) {
			page_title (intl_get('Unknown Error'));
			echo '<p>' . intl_get ('An error occurred trying to send the message.  Please try again later.') . '</p>';
			return;
		}

		page_title (intl_get ('Message Sent'));
		echo '<p>' . intl_get ('Your message has been sent.') . '</p>';
	}
}
//END: SEMIAS.
?>