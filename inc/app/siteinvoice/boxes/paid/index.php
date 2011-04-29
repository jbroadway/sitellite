<?php


	loader_import ('ext.phpmailer');

global $cgi;

foreach ($cgi->_key as $key) {
	db_execute ('update siteinvoice_invoice set status = "paid" where id = ?', $key);

	$client_id = db_shift ('select client_id from siteinvoice_invoice where id = ?', $key);
	$client = db_single ('select * from siteinvoice_client where id = ?', $client_id);
	$client->invoice_no = $key;

	$mailer = new PHPMailer ();
	$mailer->isMail ();
	$mailer->From = appconf ('company_email');
	$mailer->FromName = appconf ('company_email_name');
	$mailer->Subject = 'Payment Received for Invoice #' . $key;
	$mailer->Body = template_simple ('email/thankyou.spt', $client);
	$mailer->AddAddress ($client->contact_email, $client->contact_name);
	$mailer->Send ();

	$mailer = new PHPMailer ();
	$mailer->isMail ();
	$mailer->From = appconf ('company_email');
	$mailer->FromName = appconf ('company_email_name');
	$mailer->Subject = 'Payment Received for Invoice #' . $key;
	$mailer->Body = template_simple ('email/paid.spt', $client);
	$mailer->AddAttachment ('inc/app/siteinvoice/data/' . $key . '.pdf', strtolower ($client->code) . '-' . $key . '.pdf');
	$mailer->AddAddress (appconf ('company_email'));
	$mailer->Send ();
}

page_title ('SiteInvoice - Invoices Paid');

echo '<p><a href="' . site_prefix () . '/index/siteinvoice-app">Continue</a></p>';

?>