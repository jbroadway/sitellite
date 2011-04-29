<?php

/**
 * This is the email address to send invoices from, as well as to receive
 * blind carbon copies of the invoices and 90-day "no payment" notices.
 * This should be a working email address that can receive replies
 * regarding invoices that have been sent out.
 */
appconf_set ('company_email', 'billing@example.com');

/**
 * This is the name that will appear in the From header to clients.
 */
appconf_set ('company_email_name', 'Examples Inc. - Billing Department');

/**
 * The full company name as it should be displayed in the invoices.
 */
appconf_set ('company_name', 'Examples Inc.');

/**
 * The company address as it should be displayed in the invoices.  This
 * must stay on a single line.
 */
appconf_set ('company_address', '123 Main St., City Province, R3C 2G6');

/**
 * The company phone number as it should be displayed in the invoices.
 */
appconf_set ('company_phone', '250.555.1212');

/**
 * The company web site as it should be displayed in the invoices.
 * Note: Do not include the http:// prefix, as it will break the link
 * in the invoice.
 */
appconf_set ('company_website', 'www.example.com');

/**
 * This is an optional list of email addresses to blind carbon copy on
 * message notices.
 */
appconf_set ('bcc_list', 'person_to_bcc@example.com');

/**
 * Any extra information that should be displayed in the invoices.  This
 * will appear right-aligned underneath the invoice total.  This field
 * is often used for tax information and interest notices.
 */
appconf_set ('extra_info', 'GST REGISTRATION NUMBER 12345 6789

Fax: 1-514-555-4321');

/**
 * Here you can customize the taxes to your region.  Taxes are specified
 * as an array with the keys being the tax name (ie. GST, VAT, etc.) and
 * the value being the decimal value to multiple the subtotal by to
 * determine the tax amount.  For example, if the tax is 7%, then the
 * value would be 0.07
 */
appconf_set ('taxes', array (
	'GST' => 0.06,
	'PST' => 0.07,

));

/**
 * Here you can set the subject lines for each of the reminders.  If you
 * don't wish to have a reminder sent for one of these, simply set the
 * value to false.  Note that %s in the value will be replaced with the
 * invoice ID number.
 */
appconf_set ('reminders', array (
	30 => 'Invoice #%s - 30-day reminder',
	45 => 'Invoice #%s - 45-day reminder',
	60 => 'Invoice #%s - 60-day reminder',

));

/**
 * This is the default currency to use as a base for currency conversions.
 */
appconf_set ('default_currency', 'CAD');

/**
 * This is the list of currencies to display.
 */
appconf_set ('currencies', array (
	'CAD',
	'USD',

));

/**
 * This is a Paypal ID to allow people to pay you via Paypal directly from
 * the invoice emails.
 */
appconf_set ('paypal_id', '');

?>