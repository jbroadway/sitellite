<?php

class SitelliteExportForm extends MailForm {
	function SitelliteExportForm () {
		parent::MailForm (__FILE__);
		$user = session_get_user ();
		$groups = array ('' => '- All -');
		foreach (db_pairs ('select id, name from sitellite_form_type order by name asc') as $k => $v) {
			$groups[$k] = $v;
		}
		$this->widgets['group']->setValues ($groups);
		page_title (intl_get ('Export Contacts'));
	}

	function onSubmit ($vals) {
		if (! $vals['group']) {
			$res = db_fetch_array (
				'select distinct first_name, last_name, email_address, address_line1, address_line2, city, state, country, zip, company, job_title, phone_number, daytime_phone, evening_phone, mobile_phone, fax_number from sitellite_form_submission'
			);
			$name = 'all';
		} else {
			$res = db_fetch_array (
				'select distinct first_name, last_name, email_address, address_line1, address_line2, city, state, country, zip, company, job_title, phone_number, daytime_phone, evening_phone, mobile_phone, fax_number from sitellite_form_submission where form_type = ?',
				$vals['group']
			);
			$name = preg_replace ('/[^a-z0-9]+/', '-', strtolower (db_shift ('select name from sitellite_form_type where id = ?', $vals['group'])));
		}

		set_time_limit (0);
		header ('Cache-control: private');
		header ('Content-type: text/plain');
		header ('Content-Disposition: attachment; filename=' . $name . '-' . date ('Y-m-d') . '.csv');

		echo "First Name,Last Name,Email Address,Address Line 1,Address Line 2,City,State,Country,Zip,Company,Job Title,Phone Number,Daytime Phone,Evening Phone,Mobile Phone,Fax Number\n";

		foreach ($res as $row) {
			$r = (array) $row;
			foreach (array_keys ($r) as $k) {
				$r[$k] = str_replace ('"', '""', $r[$k]);
				if (strpos ($r[$k], ',') !== false) {
					$r[$k] = '"' . $r[$k] . '"';
				}
			}
			echo str_replace (array ("\r", "\n"), array ('\\r', '\\n'), join (',', $r)) . "\n";
		}

		exit;
	}
}

?>