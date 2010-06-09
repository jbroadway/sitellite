<?php

$loader->import ('cms.Workspace');

/**
 * @package CMS
 * @category Workspace
 */

class WorkspaceUser {

	function getList ($team = '', $role = '') {
		global $db;

		$q = '';
		$bind = array ();
		$operator = ' where ';

		if (! empty ($team)) {
			$q .= $operator . 'sitellite_team = ?';
			$bind[] = $team;
			$operator = ' and ';
		}
		if (! empty ($role)) {
			$q .= $operator . 'sitellite_role = ?';
			$bind[] = $role;
		}

		$res = $db->fetch ('
			select
				username, firstname, lastname, email
			from
				sitellite_user
			' . $q . '
			order by
				lastname, firstname, username',
			$bind
		);

		if (! $res) {
			$this->error = $db->error;
			return false;
		} elseif (is_object ($res)) {
			return array ($res);
		}
		return $res;
	}

	function getStatus ($user) {
		global $db, $loader;
		$loader->import ('saf.Date');

		$res = $db->fetch (
			'select session_id, expires from sitellite_user where username = ?',
			$user
		);

		if (! $res) {
			$this->error = $db->error;
			return false;
		}

		if (! empty ($res->session_id) && Date::timestamp ($res->expires, 'U') >= time ()) {
			return 'available';
		}
		return 'away';
	}

	function export ($user) {
		global $db, $loader;

		$row = $db->fetch ('
			select
				*
			from
				sitellite_user
			where
				username = ?',
			$user
		);

		if (! $row) {
			$this->error = $db->error;
			return false;
		}

		// build vcard

		$loader->import ('saf.Date.vCalendar');

		$card = new vCal ();
		$card->tag = 'VCARD';
		$card->addProperty ('VERSION', '3.0');
		$card->addProperty ('PRODID', '-//Sitellite CMS//NONSGML Sitellite CMS ' . SITELLITE_VERSION . '//EN');

		if (empty ($row->firstname) && empty ($row->lastname)) {
			// skip name
			$card->addProperty ('N', $row->username);
		} else {
			$card->addProperty ('N', array ($row->firstname, $row->lastname));
			$card->addProperty ('FN', $row->firstname . ' ' . $row->lastname);
		}

		if (! empty ($row->company)) {
			$card->addProperty ('ORG', $row->company);
		}

		if (! empty ($row->position)) {
			$title =& $card->addProperty ('TITLE', $row->position);
			$title->addParameter ('LANGUAGE', $row->lang);
		}

		if (! empty ($row->email)) {
			$card->addProperty ('EMAIL', $row->email, array ('type' => 'WORK'));
		}

		if (! empty ($row->phone)) {
			$card->addProperty ('TEL', $row->phone, array ('type' => 'WORK'));
		}

		if (! empty ($row->cell)) {
			$card->addProperty ('TEL', $row->cell, array ('type' => 'CELL'));
		}

		if (! empty ($row->fax)) {
			$card->addProperty ('TEL', $row->fax, array ('type' => 'FAX'));
		}

		if (! empty ($row->home)) {
			$card->addProperty ('TEL', $row->home, array ('type' => 'HOME'));
		}

		if (! empty ($row->address1)) {
			$card->addProperty (
				'ADR',
				array (
					'', '',
					$row->address1,
					$row->city,
					$row->province,
					$row->postal_code,
					$row->country
				),
				array ('type' => 'HOME')
			);
		}

		if (! empty ($row->website)) {
			$card->addProperty ('URL', $row->website);
		}

		// write the vcard

		return $card->unfold ($card->write ());
	}
}

?>