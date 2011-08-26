<?php

$o = new Order ();
$o->orderBy ('ts desc');
$p = array ();
if (! empty ($parameters['status'])) {
	$p['status'] = $parameters['status'];
}
$list = $o->find ($p);

header ('Cache-control: private');
header ('Content-Type: text/plain');
header ('Content-Disposition: attachment; filename=Orders-' . date ('Y-m-d') . '.csv');

echo "Order #,Customer Name,Status,Date/Time,Subtotal,Shipping,Taxes,Total\n";

foreach ($list as $item) {
	printf (
		"%d,%s,%s,%s,%s,%s,%s,%s\n",
		$item->id,
		$item->bill_to,
		ucwords ($item->status),
		$item->ts,
		$item->subtotal,
		$item->shipping,
		$item->taxes,
		$item->total
	);
}

exit;

?>