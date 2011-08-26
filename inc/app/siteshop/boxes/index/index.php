<?php

if (isset ($parameters['cid'])) {
	
	$alt = appconf ('alternate_category');
	if (! empty ($alt)) {
		echo loader_box ($alt, $parameters, $context);
		return;
	}
	
	// category list of products
	$c = new Category ($parameters['cid']);
	page_title ($c->val ('name'));
	echo template_simple ('category.spt', $c);
	//info ($c->getSortedProducts ());
} elseif (isset ($parameters['pid'])) {
	$alt = appconf ('alternate_product');
	if (! empty ($alt)) {
		echo loader_box ($alt, $parameters, $context);
		return;
	}

	// single product detail page
	page_add_script (site_prefix () . '/inc/app/siteshop/pix/addtocart.js');
	$p = new Product ($parameters['pid']);
	if (! $p->val ('id')) {
		page_title (intl_get ('Not Found'));
		echo '<p>' . intl_get ('The product you have requests could not be found.') . '</p>';
		return;
	}
	$prod = $p->makeObj ();
	$price = $p->getPrice ();
	if ($price != $prod->price) {
		$prod->sale = true;
		$prod->sale_price = $price;
	}
	$prod->addtocartform = $p->getAddToCartForm ();
	page_title ($p->val ('name'));
	page_description ($p->val ('description'));
	page_keywords ($p->val ('keywords'));
	page_add_script (site_prefix () . '/inc/app/siteshop/js/prototype.js');
	page_add_script (site_prefix () . '/inc/app/siteshop/js/scriptaculous.js?load=effects');
	page_add_script (site_prefix () . '/inc/app/siteshop/js/lightbox.js');
	page_add_style (site_prefix () . '/inc/app/siteshop/html/lightbox.css');
	echo template_simple ('product.spt', $prod);
} else {
	$alt = appconf ('alternate_index');
	if (! empty ($alt)) {
		echo loader_box ($alt, $parameters, $context);
		return;
	}

	// sales and top products by weight
	if (appconf ('page_title')) {
		page_title (appconf ('page_title'));
	}
	$data = array ();
	$s = new Sale ();
	if ($s->loadCurrent ()) {
		$data['sale_name'] = $s->val ('name');
		$data['sale_items'] = $s->top (3);
		$data['featured_items'] = Product::featured (3);
	} else {
		$data['sale_name'] = '';
		$data['featured_items'] = Product::featured (6);
	}
	echo template_simple ('index.spt', $data);
}

?>