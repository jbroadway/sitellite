<?php

$res = db_fetch_array ('select username, firstname, lastname, email, role, team, disabled, lang, company, position, website, phone, cell, home, fax, address1, address2, city, province, postal_code, country, registered from sitellite_user order by username asc');

set_time_limit (0);
header ('Cache-control: private');
header ('Content-type: text/plain');
header ('Content-Disposition: attachment; filename=users-' . date ('Y-m-d') . '.csv');

echo"username,firstname,lastname,email,role,team,disabled,lang,company,position,website,phone,cell,home,fax,address1,address2,city,province,postal_code,country,registered\n";

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

?>