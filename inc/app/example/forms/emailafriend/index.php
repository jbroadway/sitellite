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
//

class ExampleEmailafriendForm extends MailForm {
	function ExampleEmailafriendForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/example/forms/emailafriend/settings.php');
	}

	function onSubmit ($vals) {
		if (! empty ($vals['msg'])) {
			$vals['msg'] .= "\n\n";
		}

		// build message
		$message = array (
			'subject' => 'Interesting web site from ' . $vals['yourEmail'],
			'body' => $vals['msg']
				. "Check it out at:\n\n"
				. site_url () . $vals['url']
				. "\n\n- " . $vals['yourEmail'],
			'from' => 'From: ' . $vals['yourEmail'],
		);
//START: SEMIAS. #198 Allow for HTML mailing templates.
//-----------------------------------------------
//		if (! @mail (
//			$vals['email'],
//			$message['subject'],
//			$message['body'],
//			$message['from']
//		)) {
//-----------------------------------------------
        if (! site_mail (
			$vals['email'],
			$message['subject'],
			$message['body'],
			$message['from'],
            array ("Is_HTML" => true)
		)) {
//END: SEMIAS.
			page_title (intl_get ('Unknown Error'));
			return '<p>' . intl_get ('Your email was unable to be sent at this time.') . '</p>';
		}

		page_title (intl_get ('Thank You'));
        return '<p>' . intl_get ('Your message has been sent.') . '</p>';
	}
}

?>