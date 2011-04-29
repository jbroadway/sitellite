<?php

if (! is_writeable ('inc/app/siteinvoice/conf/properties.php')) {
	page_title ('Error');
	echo '<p>Unable to write to the inc/app/siteinvoice/conf/properties.php file.  Please adjust your filesystem permissions and try again.</p>';
	return;
}
if (is_file ('inc/app/siteinvoice/conf/properties.old.php') && ! is_writeable ('inc/app/siteinvoice/conf/properties.old.php')) {
	page_title ('Error');
	echo '<p>Unable to write to the inc/app/siteinvoice/conf/properties.old.php file.  Please adjust your filesystem permissions and try again.</p>';
	return;
}
if (! is_writeable ('inc/app/siteinvoice/conf')) {
	page_title ('Error');
	echo '<p>Unable to write to the inc/app/siteinvoice/conf folder.  Please adjust your filesystem permissions and try again.</p>';
	return;
}

class SiteinvoiceSettingsForm extends MailForm {
	var $_template = '<{split}?php

/**
 * This is the email address to send invoices from, as well as to receive
 * blind carbon copies of the invoices and 90-day "no payment" notices.
 * This should be a working email address that can receive replies
 * regarding invoices that have been sent out.
 */
appconf_set (\'company_email\', \'{company_email}\');

/**
 * This is the name that will appear in the From header to clients.
 */
appconf_set (\'company_email_name\', \'{company_email_name}\');

/**
 * The full company name as it should be displayed in the invoices.
 */
appconf_set (\'company_name\', \'{company_name}\');

/**
 * The company address as it should be displayed in the invoices.  This
 * must stay on a single line.
 */
appconf_set (\'company_address\', \'{company_address}\');

/**
 * The company phone number as it should be displayed in the invoices.
 */
appconf_set (\'company_phone\', \'{company_phone}\');

/**
 * The company web site as it should be displayed in the invoices.
 * Note: Do not include the http:// prefix, as it will break the link
 * in the invoice.
 */
appconf_set (\'company_website\', \'{company_website}\');

/**
 * This is an optional list of email addresses to blind carbon copy on
 * message notices.
 */
appconf_set (\'bcc_list\', \'{bcc_list}\');

/**
 * Any extra information that should be displayed in the invoices.  This
 * will appear right-aligned underneath the invoice total.  This field
 * is often used for tax information and interest notices.
 */
appconf_set (\'extra_info\', \'{extra_info}\');

/**
 * Here you can customize the taxes to your region.  Taxes are specified
 * as an array with the keys being the tax name (ie. GST, VAT, etc.) and
 * the value being the decimal value to multiple the subtotal by to
 * determine the tax amount.  For example, if the tax is 7%, then the
 * value would be 0.07
 */
appconf_set (\'taxes\', array (
{loop obj[taxes]}
	\'{loop/_key}\' => {loop/_value},
{end loop}
));

/**
 * Here you can set the subject lines for each of the reminders.  If you
 * don\'t wish to have a reminder sent for one of these, simply set the
 * value to false.  Note that %s in the value will be replaced with the
 * invoice ID number.
 */
appconf_set (\'reminders\', array (
{loop obj[reminders]}
	{loop/_key} => \'{loop/_value}\',
{end loop}
));

/**
 * This is the default currency to use as a base for currency conversions.
 */
appconf_set (\'default_currency\', \'{default_currency}\');

/**
 * This is the list of currencies to display.
 */
appconf_set (\'currencies\', array (
{loop obj[currencies]}
	\'{loop/_value}\',
{end loop}
));

/**
 * This is a Paypal ID to allow people to pay you via Paypal directly from
 * the invoice emails.
 */
appconf_set (\'paypal_id\', \'{paypal_id}\');

?{split}>';
	function SiteinvoiceSettingsForm () {
		parent::MailForm (__FILE__);
		page_title ('SiteInvoice - Settings');
		$this->widgets['company_name']->setDefault (appconf ('company_name'));
		$this->widgets['company_email']->setDefault (appconf ('company_email'));
		$this->widgets['company_email_name']->setDefault (appconf ('company_email_name'));
		$this->widgets['company_website']->setDefault (appconf ('company_website'));
		$this->widgets['company_phone']->setDefault (appconf ('company_phone'));
		$this->widgets['company_address']->setDefault (appconf ('company_address'));
		$this->widgets['bcc_list']->setDefault (appconf ('bcc_list'));
		$this->widgets['extra_info']->setDefault (appconf ('extra_info'));
		$taxes = appconf ('taxes');
		foreach ($taxes as $k => $v) {
			$taxes[$k] = $v * 100;
		}
		$this->widgets['taxes']->setDefault ($taxes);
		$this->widgets['reminders']->setDefault (appconf ('reminders'));
		$this->widgets['default_currency']->setDefault (appconf ('default_currency'));
		$this->widgets['currencies']->setDefault (join (', ', appconf ('currencies')));
		$this->widgets['paypal_id']->setDefault (appconf ('paypal_id'));
	}

	function onSubmit ($vals) {
		loader_import ('saf.File');

		foreach ($vals['taxes'] as $k => $v) {
			$vals['taxes'][$k] = $v / 100;
		}

		$vals['currencies'] = preg_split ('/, ?/', $vals['currencies']);

		file_overwrite ('inc/app/siteinvoice/conf/properties.old.php', join ('', file ('inc/app/siteinvoice/conf/properties.php')));
		file_overwrite ('inc/app/siteinvoice/conf/properties.php', template_simple ($this->_template, $vals));

		umask (0000);
		@chmod ('inc/app/siteinvoice/conf/properties.old.php', 0777);
		@chmod ('inc/app/siteinvoice/conf/properties.php', 0777);

		page_title ('SiteInvoice - Settings Saved');
		echo '<p><a href="' . site_prefix () . '/index/siteinvoice-app">Continue</a></p>';
	}
}

?>