<?php

loader_import ('saf.Date');
loader_import ('ext.phpmailer');

$mailer = new PHPMailer ();
$mailer->isMail ();

$today = date ('Y-m-d');
$thirty = Date::subtract ($today, '30 day') . ' 00:00:00';
$forty_five = Date::subtract ($today, '45 day') . ' 00:00:00';
$sixty = Date::subtract ($today, '60 day') . ' 00:00:00';
$ninety = Date::subtract ($today, '90 day') . ' 00:00:00';
$reminders = appconf ('reminders');

// 30 days past due -- email reminder
if ($reminders[30]) {
	$res = db_fetch_array (
		'select * from siteinvoice_invoice where status = "unpaid" and notice < 30 and sent_on <= ? and sent_on > ?',
		$thirty,
		$forty_five
	);

	foreach ($res as $row) {
		$client = db_single ('select * from siteinvoice_client where id = ?', $row->client_id);
		$client->invoice_no = $row->id;
		$client->total = $row->total;
		$client->currency = $row->currency;

		$mailer->ClearAllRecipients ();
		$mailer->ClearAttachments ();
		$mailer->From = appconf ('company_email');
		$mailer->FromName = appconf ('company_email_name');
		$mailer->Subject = sprintf ($reminders[30], $row->id);
		$bcc_list = appconf ('bcc_list');
		if (! empty ($bcc_list)) {
			$bcc = appconf ('company_email') . ', ' . $bcc_list;
		} else {
			$bcc = appconf ('company_email');
		}
		$mailer->AddBCC ($bcc);
		$mailer->Body = template_simple ('email/30-days.spt', $client);
		$mailer->AddAttachment ('inc/app/siteinvoice/data/' . $row->id . '.pdf', strtolower ($client->code) . '-' . $row->id . '.pdf');
		$mailer->AddAddress ($client->contact_email, $client->contact_name);
		$mailer->Send ();

		db_execute ('update siteinvoice_invoice set notice = 30 where id = ?', $row->id);
	}
}

// 45 days past due -- email reminder
if ($reminders[45]) {
	$res = db_fetch_array (
		'select * from siteinvoice_invoice where status = "unpaid" and notice < 45 and sent_on <= ? and sent_on > ?',
		$forty_five,
		$sixty
	);

	foreach ($res as $row) {
		$client = db_single ('select * from siteinvoice_client where id = ?', $row->client_id);
		$client->invoice_no = $row->id;
		$client->total = $row->total;
		$client->currency = $row->currency;
	
		$mailer->ClearAllRecipients ();
		$mailer->ClearAttachments ();
		$mailer->From = appconf ('company_email');
		$mailer->FromName = appconf ('company_email_name');
		$mailer->Subject = sprintf ($reminders[45], $row->id);
		$bcc_list = appconf ('bcc_list');
		if (! empty ($bcc_list)) {
			$bcc = appconf ('company_email') . ', ' . $bcc_list;
		} else {
			$bcc = appconf ('company_email');
		}
		$mailer->AddBCC ($bcc);
		$mailer->Body = template_simple ('email/45-days.spt', $client);
		$mailer->AddAttachment ('inc/app/siteinvoice/data/' . $row->id . '.pdf', strtolower ($client->code) . '-' . $row->id . '.pdf');
		$mailer->AddAddress ($client->contact_email, $client->contact_name);
		$mailer->Send ();
	
		db_execute ('update siteinvoice_invoice set notice = 45 where id = ?', $row->id);
	}
}

// 60 days past due -- email notice
if ($reminders[60]) {
	$res = db_fetch_array (
		'select * from siteinvoice_invoice where status = "unpaid" and notice < 60 and sent_on <= ? and sent_on > ?',
		$sixty,
		$ninety
	);
	
	foreach ($res as $row) {
		$client = db_single ('select * from siteinvoice_client where id = ?', $row->client_id);
		$client->invoice_no = $row->id;
		$client->total = $row->total;
		$client->currency = $row->currency;
	
		$mailer->ClearAllRecipients ();
		$mailer->ClearAttachments ();
		$mailer->From = appconf ('company_email');
		$mailer->FromName = appconf ('company_email_name');
		$mailer->Subject = sprintf ($reminders[60], $row->id);
		$bcc_list = appconf ('bcc_list');
		if (! empty ($bcc_list)) {
			$bcc = appconf ('company_email') . ', ' . $bcc_list;
		} else {
			$bcc = appconf ('company_email');
		}
		$mailer->AddBCC ($bcc);
		$mailer->Body = template_simple ('email/60-days.spt', $client);
		$mailer->AddAttachment ('inc/app/siteinvoice/data/' . $row->id . '.pdf', strtolower ($client->code) . '-' . $row->id . '.pdf');
		$mailer->AddAddress ($client->contact_email, $client->contact_name);
		$mailer->Send ();
	
		db_execute ('update siteinvoice_invoice set notice = 60 where id = ?', $row->id);
	}
}

// 90 days past due -- email admin only

$res = db_fetch_array (
	'select * from siteinvoice_invoice where status = "unpaid" and notice < 90 and sent_on <= ?',
	$ninety
);

foreach ($res as $row) {
	$client = db_single ('select * from siteinvoice_client where id = ?', $row->client_id);
	$client->invoice_no = $row->id;
	$client->total = $row->total;
	$client->currency = $row->currency;

	$mailer->ClearAllRecipients ();
	$mailer->ClearAttachments ();
	$mailer->From = appconf ('company_email');
	$mailer->FromName = appconf ('company_email_name');
	$mailer->Subject = 'Overdue Invoice #' . $row->id;
	$mailer->Body = template_simple('email/90-days.spt', $client);
	$mailer->AddAttachment ('inc/app/siteinvoice/data/' . $row->id . '.pdf', strtolower ($client->code) . '-' . $row->id . '.pdf');
	$mailer->AddAddress (appconf ('company_email'));
	$mailer->AddReplyTo ($client->contact_email, $client->contact_name);
	$bcc = appconf ('bcc_list');
	if (! empty ($bcc)) {
		$mailer->AddBCC ($bcc);
	}
	$mailer->Send ();

	db_execute ('update siteinvoice_invoice set notice = 90 where id = ?', $row->id);
}

?>