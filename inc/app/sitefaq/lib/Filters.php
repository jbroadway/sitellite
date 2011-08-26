<?php

function sitefaq_facet_age () {
	$list = appconf ('user_age_list');
	if (! is_array ($list)) {
		global $loader;
		$loader->apps[] = 'sitefaq';
		include_once ('inc/app/sitefaq/conf/properties.php');
		$list = appconf ('user_age_list');
		array_shift ($loader->apps);
	}
	array_shift ($list);
	return $list;
}

function sitefaq_facet_assigned_to () {
	$res = db_shift_array ('select distinct assigned_to from sitefaq_submission order by assigned_to asc');
	$ret = array ();
	foreach ($res as $a) {
		if (empty ($a)) {
			continue;
			$ret[''] = intl_get ('None');
		} else {
			$ret[$a] = db_shift ('select concat(lastname, ", ", firstname, " (", username, ")") from sitellite_user where username = ?', $a);
		}
	}
	return $ret;
}

function sitefaq_virtual_add_faq (&$obj) {
	return template_simple (
		'<a href="{site/prefix}/index/cms-add-form?collection=sitefaq_question&question={filter urlencode}{question}&answer={answer}{end filter}">
			<img src="{site/prefix}/inc/app/sitefaq/pix/add.gif" border="0"
				alt="{intl Add to live FAQs}"
				title="{intl Add to live FAQs}" /></a>',
		$obj
	);
}

function sitefaq_filter_date_time ($date) {
	return Date::timestamp ($date, appconf ('format_date_time'));
}

?>