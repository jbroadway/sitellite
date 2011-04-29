<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #177 Pagination.
//

if (! session_admin ()) {
	page_title ( 'Invoices - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="siteinvoice-app" />
		<table cellpadding="5" border="0">
			<tr>
				<td>Username</td>
				<td><input type="text" name="username" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" name="password" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Enter" /></td>
			</tr>
		</table>
		</form>'
	);

	return;
}

// For those pear and ext packages that use the include_path:
if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
	$join = ';';
} else {
	$join = ':';
}
ini_set ('include_path', 'inc/app/siteinvoice/lib/PEAR' . $join . ini_get ('include_path'));

loader_import ('siteinvoice.PEAR.Services.ExchangeRates');

global $cgi;

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
}

$sql = 'select * from siteinvoice_invoice';
$sep = ' where ';

if (! $cgi->client) {
	$cgi->client = '';
} else {
	$sql .= $sep . 'client_id = ' . db_quote ($cgi->client);
	$sep = ' and ';
}

if (! $cgi->status) {
	$cgi->status = 'unpaid';
}

$sql .= $sep . 'status = ' . db_quote ($cgi->status);

$sql .= ' order by sent_on asc';

page_title ('SiteInvoice - Invoices');

echo template_simple ('nav.spt');

loader_import ('siteinvoice.Filters');
loader_import ('saf.Date');
loader_import ('saf.GUI.Pager');

$q = db_query ($sql);

if (! $q->execute ()) {
	$total = 0;
	$invoices = array ();
} else {
	$total = $q->rows ();
	$invoices = $q->fetch ($cgi->offset, 20);
}
$q->free ();
// Start: SEMIAS #177 Pagination.
//	Not sure a fix is needed here.	
$pg = new Pager ($cgi->offset, 20, $total);
$pg->setUrl (
	site_current () . '?client=%s&status=%s',
	$cgi->client,
	$cgi->status
);
$pg->update ();
// END: SEMIAS
$dc = appconf ('default_currency');
$exch = new Services_ExchangeRates (
	'ECB', 'UN', 'UN', array (
		'roundToDecimal' => 2,
		'roundAutomatically' => true,
		'thousandsSeparator' => '',
		'decimalCharacter' => '.',
		'cacheDirectory' => 'inc/app/siteinvoice/data/rates/',
		'cacheLengthRates' => 86400,
		'cacheLengthCurrencies' => 2592000,
		'cacheLengthCountries' => 2592000,
		//'pearErrorMode' => 0,
	)
);
$subtotal = 0;
$taxes = 0;
$total = 0;

if ($cgi->status == 'unpaid') {
	$today = date ('Y-m-d');
	$thirty = Date::subtract ($today, '30 day') . ' 00:00:00';
	$forty_five = Date::subtract ($today, '45 day') . ' 00:00:00';
	$sixty = Date::subtract ($today, '60 day') . ' 00:00:00';
	$ninety = Date::subtract ($today, '90 day') . ' 00:00:00';

	foreach (array_keys ($invoices) as $k) {
		if ($invoices[$k]->sent_on < $ninety) {
			$invoices[$k]->range = 90;
		} elseif ($invoices[$k]->sent_on < $sixty) {
			$invoices[$k]->range = 60;
		} elseif ($invoices[$k]->sent_on < $forty_five) {
			$invoices[$k]->range = 45;
		} elseif ($invoices[$k]->sent_on < $thirty) {
			$invoices[$k]->range = 30;
		} else {
			$invoices[$k]->range = 0;
		}

		if ($invoices[$k]->currency != $dc) {
			//$invoices[$k]->e_subtotal = $invoices[$k]->subtotal;
			//$invoices[$k]->e_taxes = $invoices[$k]->taxes;
			$invoices[$k]->e_total = number_format ($invoices[$k]->total, 2);
			$invoices[$k]->subtotal = $exch->convert ($invoices[$k]->currency, $dc, $invoices[$k]->subtotal, false);
			$invoices[$k]->taxes = $exch->convert ($invoices[$k]->currency, $dc, $invoices[$k]->taxes, false);
			$invoices[$k]->total = $exch->convert ($invoices[$k]->currency, $dc, $invoices[$k]->total, false);
		} else {
			//$invoices[$k]->e_subtotal = false;
			//$invoices[$k]->e_taxes = false;
			$invoices[$k]->e_total = false;
		}

		$subtotal += $invoices[$k]->subtotal;
		$taxes += $invoices[$k]->taxes;
		$total += $invoices[$k]->total;
		$invoices[$k]->subtotal = number_format ($invoices[$k]->subtotal, 2);
		$invoices[$k]->taxes = number_format ($invoices[$k]->taxes, 2);
		$invoices[$k]->total = number_format ($invoices[$k]->total, 2);
	}
} else {
	foreach (array_keys ($invoices) as $k) {
		$invoices[$k]->range = 0;

		if ($invoices[$k]->currency != $dc) {
			$invoices[$k]->e_total = number_format ($invoices[$k]->total, 2);
			$invoices[$k]->subtotal = $exch->convert ($invoices[$k]->currency, $dc, $invoices[$k]->subtotal, false);
			$invoices[$k]->taxes = $exch->convert ($invoices[$k]->currency, $dc, $invoices[$k]->taxes, false);
			$invoices[$k]->total = $exch->convert ($invoices[$k]->currency, $dc, $invoices[$k]->total, false);
		} else {
			$invoices[$k]->e_total = false;
		}

		$subtotal += $invoices[$k]->subtotal;
		$taxes += $invoices[$k]->taxes;
		$total += $invoices[$k]->total;
		$invoices[$k]->subtotal = number_format ($invoices[$k]->subtotal, 2);
		$invoices[$k]->taxes = number_format ($invoices[$k]->taxes, 2);
		$invoices[$k]->total = number_format ($invoices[$k]->total, 2);
	}
}

$subtotal = number_format ($subtotal, 2);
$taxes = number_format ($taxes, 2);
$total = number_format ($total, 2);

template_simple_register ('pager', $pg);
echo template_simple (
	'invoices.spt',
	array (
		'invoices' => $invoices,
		'clients' => db_pairs ('select id, name from siteinvoice_client order by name asc'),
		'client' => $cgi->client,
		'status' => $cgi->status,
		'subtotal' => $subtotal,
		'taxes' => $taxes,
		'total' => $total,
	)
);

?>