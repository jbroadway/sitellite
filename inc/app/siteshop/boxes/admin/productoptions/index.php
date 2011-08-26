<?php

loader_import('siteshop.Objects');

global $cgi;

if (isset($cgi->submit_button)) {

	$p = new Product($cgi->p);
	$options = $p->getAllOptions();
	
	foreach ($cgi->param as $to) {
		if (strpos($to, 'po-') === 0) {
			list($ignore, $id, $option) = explode ('-',$to);
			
			foreach ($options as $k=>$o) {
				if ($o->id == $id && $option == 'yes') {
					echo 'yes';
					$options[$k]->yes =  true;
				} elseif ($o->id == $id && $option == 'no') {
					echo 'no';
					$options[$k]->no = true;
				}
			}
		}
	}
	
	foreach ($options as $o) {
		//unavailable
		if (isset ($o->no)) {
			$p->setOptionC ($o->id, 'no');
		//available
		} elseif (isset ($o->yes)) {
			$p->setOptionC ($o->id, 'yes');
		//unlisted
		} else {
			$p->setOptionC ($o->id, null);
		}
	}
	
	header ('Location: ' . site_prefix() . '/index/siteshop-admin-products-action');
	exit;
}

page_title ('SiteShop 2 - ' . intl_get ('Product Options'));

echo loader_box ('siteshop/admin/nav', array ('current' => 'products'));
global $cgi;
$p = new Product($cgi->p);
$options = $p->getAllOptions();
$parameters['name'] = $p->val('name');
$parameters['product_id'] = $cgi->p;

echo template_simple ('admin_productoptions1.spt', $parameters);

$previous_type = $options[0]->type;
$data = array ();
foreach ($options as $o) {
	if ($o->type != $previous_type) {
		echo  template_simple ('admin_productoptions2.spt', array ('options' => $data, 'type' => $previous_type));
		$previous_type = $o->type;
		$data = array ();
		$data[] = $o;
	} else {
		$data[] = $o;
	}
}

echo  template_simple ('admin_productoptions2.spt', array ('options' => $data, 'type' => $previous_type));

echo template_simple ('admin_productoptions3.spt');
?>