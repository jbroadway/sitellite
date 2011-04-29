<?php

$user = db_single ('select * from sitellite_user where username = ?', $parameters['user']);
if (! $user) {
	die ('User not found');
}

loader_import ('saf.Date.vCalendar');

$card = new vCal ();
$card->tag = 'VCARD';

$card->addProperty ('VERSION', '3.0');
$card->addProperty ('FN', $user->firstname . ' ' . $user->lastname);
$card->addProperty ('N', array ($user->lastname, $user->firstname));
if (! empty ($user->company)) {
	$card->addProperty ('ORG', $user->company);
}
if (! empty ($user->website)) {
	$card->addProperty ('URL', $user->website, array ('TYPE' => 'WORK'));
}
if (! empty ($user->email)) {
	$card->addProperty ('EMAIL', $user->email, array ('PREF' => 'INTERNET'));
}
if (! empty ($user->phone)) {
	$card->addProperty ('TEL', $user->phone, array ('TYPE' => 'WORK'));
}
if (! empty ($user->cell)) {
	$card->addProperty ('TEL', $user->cell, array ('TYPE' => 'CELL'));
}
if (! empty ($user->home)) {
	$card->addProperty ('TEL', $user->home, array ('TYPE' => 'HOME'));
}
if (! empty ($user->address1)) {
	$card->addProperty ('item1.ADR', array ($user->address1, $user->address2, $user->city, $user->province, $user->postal_code, $user->country), array ('TYPE' => 'WORK'));
}
$card->addProperty ('X-SITELLITE', $user->username, array ('TYPE' => 'WORK'));

// phone & address info...

header ('Content-Type: text/x-vcard');
header ('Content-Disposition: attachment; filename="' . $user->firstname . ' ' . $user->lastname . '.vcf');
echo $card->unfold ($card->write ());
exit;

?>