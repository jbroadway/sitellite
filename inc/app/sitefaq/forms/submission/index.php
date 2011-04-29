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

class SitefaqSubmissionForm extends MailForm {
	function SitefaqSubmissionForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/sitefaq/forms/submission/settings.php');

		page_title (intl_get ('Ask a Question'));

		if (! appconf ('user_details')) {
			unset ($this->widgets['name']);
			unset ($this->widgets['age']);
			unset ($this->widgets['url']);
		}

		if (appconf ('user_email_not_required')) {
			array_shift ($this->widgets['email']->rules);
		}
	}

	function onSubmit ($vals) {
		// 1. insert into sitefaq_submission table
		if ($vals['url'] == 'http://') {
			$vals['url'] = '';
		}

		$member_id = session_username ();
		if (! $member_id) {
			$member_id = '';
		}

		if (! $vals['name']) {
			$vals['name'] = '';
		}

		if (! $vals['age']) {
			$vals['age'] = '';
		}

		if (! $vals['url']) {
			$vals['url'] = '';
		}

		db_execute (
			'insert into sitefaq_submission
				(id, question, answer, ts, assigned_to, email, member_id, ip, name, age, url, sitellite_status, sitellite_access, sitellite_owner, sitellite_team)
			values
				(null, ?, "", now(), "", ?, ?, ?, ?, ?, ?, "draft", "private", "", "none")',
			$vals['question'],
			$vals['email'],
			$member_id,
			$_SERVER['REMOTE_ADDR'],
			$vals['name'],
			$vals['age'],
			$vals['url']
		);

		// 2. email all admins
		$admin_roles = session_admin_roles ();
		$emails = db_shift_array (
			'select distinct email from sitellite_user
			where role in("' . join ('", "', $admin_roles) . '")'
		);
//START: SEMIAS. #198 Allow for HTML mailing templates.
//-----------------------------------------------
/*
		foreach ($emails as $email) {
			@mail (
				$email,
				intl_get ('FAQ Submission Notice'),
				template_simple ('email_notice.spt', $vals),
				'From: faq@' . str_replace ('www.', '', site_domain ())
			);
		}

		// 4. if the user provided an email address, send a thank you
		if (! empty ($vals['email'])) {
			@mail (
				$vals['email'],
				intl_get ('FAQ Submission Received'),
				template_simple ('email_thank_you.spt', $vals),
				'From: faq@' . str_replace ('www.', '', site_domain ())
			);
		}
*/
//-----------------------------------------------
        foreach ($emails as $email) {
			site_mail (
				$email,
				intl_get ('FAQ Submission Notice'),
				template_simple ('email_notice.spt', $vals),
				'From: faq@' . str_replace ('www.', '', site_domain ()),
                array ("Is_HTML" => true)
			);
		}

		// 4. if the user provided an email address, send a thank you
		if (! empty ($vals['email'])) {
			site_mail (
				$vals['email'],
				intl_get ('FAQ Submission Received'),
				template_simple ('email_thank_you.spt', $vals),
				'From: faq@' . str_replace ('www.', '', site_domain ()),
                array ("Is_HTML" => true)
			);
		}
//END: SEMIAS.

		// 3. output a thank you
		page_title (intl_get ('Thank You'));

		echo template_simple ('thank_you.spt', $vals);
	}
}

?>