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

class SitelliteSendForm extends MailForm {
	function SitelliteSendForm () {
		parent::MailForm (__FILE__);
		$user = session_get_user ();
        $this->widgets['submit_button']->buttons[1]->extra = 'onclick="history.go (-1); return false"';
		$this->widgets['from_email']->setValue ($user->email);
		$this->widgets['from_name']->setValue ($user->firstname . ' ' . $user->lastname);
		$groups = array ('' => '- All -');
		foreach (db_pairs ('select id, name from sitellite_form_type order by name asc') as $k => $v) {
			$groups[$k] = $v;
		}
		$this->widgets['send_to']->setValues ($groups);
		page_title (intl_get ('Send Email'));
        if (appconf ('use_wysiwyg_editor')) {
			$this->widgets['message'] =& $this->widgets['message']->changeType ('tinyarea');
			$this->widgets['message']->tinyPathLocation = '';
			$this->widgets['message']->tinyButtons1 = 'bold,italic,underline,justifyleft,justifycenter,justifyright,bullist,numlist,link,unlink,emotions,undo,redo,formatselect';
			$this->widgets['message']->alt = '';
		}
	}

	function onSubmit ($vals) {
		$sql = 'select distinct email_address from sitellite_form_submission';
		if ($vals['include_no_consent']) {
			$sql .= ' where (may_we_contact_you is null or may_we_contact_you = "yes")';
		} else {
			$sql .= ' where may_we_contact_you = "yes"';
		}
		if ($vals['send_to']) {
			$sql .= ' and form_type = ' . db_quote ($vals['send_to']);
		}
		$emails = db_shift_array ($sql);

		set_time_limit (0);
//START: SEMIAS. #198 Allow for HTML mailing templates.
//-----------------------------------------------
/*
		foreach ($emails as $email) {
			// send email
			@mail (
				$email,
				$vals['subject'],
				$vals['message'],
				'From: ' . $vals['from_name'] . ' <' . $vals['from_email'] . ">\r\n"
			);
		}

		// send copy to sender
		@mail (
			$vals['from_email'],
			$vals['subject'],
			$vals['message'],
			'From: ' . $vals['from_name'] . ' <' . $vals['from_email'] . ">\r\n"
		);
*/
//-----------------------------------------------
        foreach ($emails as $email) {
			// send email
			site_mail (
				$email,
				$vals['subject'],
				$vals['message'],
				'From: ' . $vals['from_name'] . ' <' . $vals['from_email'] . ">\r\n",
                array ("Is_HTML" => true)
			);
		}

		// send copy to sender
		site_mail (
			$vals['from_email'],
			$vals['subject'],
			$vals['message'],
			'From: ' . $vals['from_name'] . ' <' . $vals['from_email'] . ">\r\n",
            array ("Is_HTML" => true)
		);
//END: SEMIAS.
		page_title (intl_get ('Email Sent'));
		echo '<p>' . intl_get ('Email sent to') . ' ' . count ($emails) . ' ' . intl_get ('recipients') . '.</p>';
		echo '<p><a href="' . site_prefix () . '/index/cms-browse-action?collection=sitellite_form_submission">Continue</a></p>';
	}
}

?>