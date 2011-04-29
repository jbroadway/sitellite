<?php

function siteshop_link_title ($title) {
	return trim (strtolower (
		preg_replace (
			'/[^a-zA-Z0-9]+/',
			'-',
			$title
		)
	), '-');
}

function siteshop_currency () {
	$c = appconf ('currency_code');
	if (! empty ($c)) {
		return $c;
	}
	$ini = ini_parse ('inc/app/siteshop/conf/settings.php');
	return $ini['General']['currency_code'];
}

function siteshop_filter_availability ($a) {
	$list = appconf ('availability');
	return $list[$a];
}

function siteshop_filter_weight ($a) {
	$list = appconf ('weight');
	return $list[$a];
}

function siteshop_filter_categories ($id) {
	$p = new Product ($id);
	$cats = $p->getCategories ();
	$o = '';
	$sep = '';
	foreach ($cats as $cat) {
		$o .= $sep . $cat->name;
		$sep = ', ';
	}
	return $o;
}

function siteshop_filter_sale_products ($id) {
	$prods = db_fetch_array (
		'select s.product_id, s.sale_price, p.name from siteshop_sale_product s, siteshop_product p where s.sale_id = ? and s.product_id = p.id',
		$id
	);
	$o = '';
	foreach ($prods as $p) {
		$o .= $p->name . ' ($' . $p->sale_price . ')<br />';
	}
	return $o;
}

function siteshop_filter_category_products ($id) {
	return db_shift (
		'select count(*) from siteshop_product_category where category_id = ?',
		$id
	);
}

function siteshop_filter_date ($d) {
	return date ('F j, Y', strtotime ($d));
}

function siteshop_filter_date_time ($d) {
	return date ('F j, Y - g:ia', strtotime ($d));
}

function siteshop_filter_money ($d) {
	return money_format ('%^!n', $d);
}

function siteshop_filter_province ($c) {
	$n = db_shift ('select province from siteshop_province where code = ?', $c);
	if (! empty ($n)) {
		return $n;
	}
	return $c;
}

function siteshop_filter_country ($c) {
	$n = db_shift ('select country from siteshop_country where code = ?', $c);
	if (! empty ($n)) {
		return $n;
	}
	return $c;
}

function siteshop_filter_product_name ($id) {
	return db_shift (
		'select name from siteshop_product where id = ?',
		$id
	);
}


function siteshop_filter_option_type_options ($t) {
	$res = db_fetch_array ('select id, name from siteshop_option where type = ? order by weight asc, name asc', $t);

	$oa = array();
	$o = '';
	foreach ($res as $r) {
		$oa[] = '<a href="' . site_prefix() . '/index/siteshop-edit-option-form?id=' . $r->id . '">' . $r->name . '</a><a href="' . site_prefix() . '/index/siteshop-admin-options-removeone-action?id=' . $r->id . '"><img src="' . site_prefix() .'/inc/app/cms/pix/icons/delete.gif" alt="' . intl_get('Delete Selected') . '" title="' . intl_get('Delete Selected') . '" border="0" onclick="return siteshop_delete_confirm ()" /></a>';
	}

	return implode (', ', $oa);
}


function siteshop_get_states ($s = '') {
	
	$out = array ('' => '- ' . intl_get ('SELECT') . '-');
	$list = db_pairs ('select code, province from siteshop_province where active = "yes"');
	foreach ($list as $k => $v) {
		$out[$k] = $v;
	}
	$options = '';
	
	foreach ($out as $k => $v) {
		if ($s == $k) {
				$options .= '<option selected="true" value="' . $k . '">' . $v . "</option>\n";
		} else {
			$options .= '<option value="' . $k . '">' . $v . "</option>\n";
		}
	}
	return $options;
}

function siteshop_get_countries ($s = '') {
	$out = array ('' => '- ' . intl_get ('SELECT') . '-');
	$list = db_pairs ('select code, country from siteshop_country where active = "yes"');
	foreach ($list as $k => $v) {
		$out[$k] = $v;
	}
	$options = '';
		foreach ($out as $k => $v) {
		if ($s == $k) {
				$options .= '<option selected="true" value="' . $k . '">' . $v . "</option>\n";
		} else {
			$options .= '<option value="' . $k . '">' . $v . "</option>\n";
		}
	}
	
	return $options;
}


?>