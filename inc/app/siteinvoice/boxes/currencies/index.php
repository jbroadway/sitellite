<?php

// For those pear and ext packages that use the include_path:
if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
	$join = ';';
} else {
	$join = ':';
}
ini_set ('include_path', 'inc/app/siteinvoice/lib/PEAR' . $join . ini_get ('include_path'));

loader_import ('siteinvoice.PEAR.Services.ExchangeRates');

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

$currencies = array ();
$selected = preg_split ('/, ?/', $parameters['sel']);

foreach ($exch->validCurrencies as $k => $v) {
	$sel = in_array ($k, $selected) ? true : false;
	$currencies[] = array (
		'code' => $k,
		'name' => $v,
		'sel' => $sel,
	);
}

page_title ('Currencies');

echo template_simple (
	'currencies.spt',
	array (
		'currencies' => $currencies,
	)
);

?>