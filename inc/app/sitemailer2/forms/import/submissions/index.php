<?php

class Sitemailer2ImportSubmissionsForm extends MailForm {
	function Sitemailer2ImportSubmissionsForm () {
		parent::MailForm (__FILE__);
		$user = session_get_user ();
		$groups = array ('' => '- All -');
		foreach (db_pairs ('select id, name from sitellite_form_type order by name asc') as $k => $v) {
			$groups[$k] = $v;
		}
		$this->widgets['group']->setValues ($groups);
		$this->widgets['newsletter']->setValues (db_pairs ('select id, name from sitemailer2_newsletter order by name asc'));
		page_title (intl_get ('Add to Newsletter'));
	}

	function onSubmit ($vals) {
		if (! $vals['group']) {
			$res = db_fetch_array (
				'select distinct first_name, last_name, email_address, company from sitellite_form_submission'
			);
		} else {
			$res = db_fetch_array (
				'select distinct first_name, last_name, email_address, company from sitellite_form_submission where form_type = ?',
				$vals['group']
			);
		}

		foreach ($res as $row) {
			$id = db_shift ('select id from sitemailer2_recipient where email = ?', $row->email_address);
			if (! $id) {
				$row->first_name = (is_null ($row->first_name)) ? '' : $row->first_name;
				$row->last_name = (is_null ($row->last_name)) ? '' : $row->last_name;
				$row->company = (is_null ($row->company)) ? '' : $row->company;
				db_execute (
					'insert into sitemailer2_recipient values (null, ?, ?, ?, ?, "", now(), "active")',
					$row->email_address,
					$row->first_name,
					$row->last_name,
					$row->company
				);
				$id = db_lastid ();
			}
			db_execute (
				'insert into sitemailer2_recipient_in_newsletter values (?, ?, now(), "subscribed")',
				$id,
				$vals['newsletter']
			);
		}

		page_title (intl_get ('Contacts Added'));
		echo '<p>' . intl_get ('The selected users have been added to your newsletter.') . ' <a href="/index/cms-browse-action?collection=sitellite_form_submission">' . intl_get ('Back') . '</a></p>';
	}
}

?>