<?php

function sitemailer2_get_roles () {
	$snm =& session_get_manager ();
	$list = array ('' => '- SELECT -');
	foreach (array_keys ($snm->role->getList ()) as $role) {
		$list[$role] = ucwords ($role);
	}
	return $list;
}

function sitemailer2_get_teams () {
	$snm =& session_get_manager ();
	$list = array ('' => '- SELECT -');
	foreach (array_keys ($snm->team->getList ()) as $role) {
		$list[$role] = ucwords ($role);
	}
	return $list;
}

class Sitemailer2ImportUsersForm extends MailForm {
	function Sitemailer2ImportUsersForm () {
		parent::MailForm (__FILE__);
		$this->widgets['newsletter']->setValues (db_pairs ('select id, name from sitemailer2_newsletter order by name asc'));
		page_title (intl_get ('Add to Newsletter'));
	}

	function onSubmit ($vals) {
		$sql = 'select email, firstname, lastname, company, website from sitellite_user';
		$clause = ' where ';
		$bind = array ();

		if (! empty ($vals['team'])) {
			$sql .= $clause . 'team = ?';
			$clause = ' and ';
			$bind[] = $vals['team'];
		}

		if (! empty ($vals['role'])) {
			$sql .= $clause . 'role = ?';
			$bind[] = $vals['role'];
		}

		$res = db_fetch_array ($sql, $bind);

		foreach ($res as $row) {
			$id = db_shift ('select id from sitemailer2_recipient where email = ?', $row->email);
			if (! $id) {
				db_execute (
					'insert into sitemailer2_recipient values (null, ?, ?, ?, ?, ?, now(), "active")',
					$row->email,
					$row->firstname,
					$row->lastname,
					$row->company,
					$row->website
				);
				$id = db_lastid ();
			}
			db_execute (
				'insert into sitemailer2_recipient_in_newsletter values (?, ?, now(), "subscribed")',
				$id,
				$vals['newsletter']
			);
		}

		page_title (intl_get ('Users Added'));
		echo '<p>' . intl_get ('The selected users have been added to your newsletter.') . ' <a href="/index/usradm-browse-action?list=users">' . intl_get ('Back') . '</a></p>';
	}
}

?>