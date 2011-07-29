<?php

if (! $parameters['package'] || empty ($parameters['package'])) {
	$parameters['package'] = 'saf';
}
if (strpos ($parameters['package'], '.') === false) {
	$parameters['current'] = $parameters['package'];
} else {
	$parameters['current'] = array_shift (explode ('.', $parameters['package']));
}

// this block will forward requests based on rules defined in conf/properties.php
$forward_rules = appconf ('forward_rules');
if (isset ($forward_rules[$parameters['package'] . ':' . $parameters['class']])) {
	if (strpos ($forward_rules[$parameters['package'] . ':' . $parameters['class']], 'http://') === 0) {
		// it's a url
		echo '<h1>' . $parameters['class'] . '</h1>';
		echo '<p>This is a 3rd-party class, found at this address:</p>';
		echo '<p><a href="' . $forward_rules[$parameters['package'] . ':' . $parameters['class']] . '">' . $forward_rules[$parameters['package'] . ':' . $parameters['class']] . '</a></p>';
		return;
	} else {
		list ($pkg, $cls) = explode (':', $forward_rules[$parameters['package'] . ':' . $parameters['class']]);
		header ('Location: ' . site_prefix () . '/index/docs-app/package.' . $pkg . '/class.' . $cls);
		exit;
	}
} elseif ($parameters['class'] == 'Generic' && $parameters['package'] != 'saf.Database') {
	header ('Location: ' . site_prefix () . '/index/docs-app/package.saf.Database/class.Generic');
	exit;
}

loader_import ('docs.Docs');
loader_import ('docs.Filters');
loader_import ('docs.Functions');

$data = array ();

$data['packages'] = docs_cache ('packages', $parameters['current']);
if (! is_array ($data['packages'])) {
	$data['packages'] = docs_cache_store (Docs::packages ($parameters['current']), 'packages');
}

if (strpos ($parameters['package'], '.') !== false) {
	if (! in_array ($parameters['package'], $data['packages']) && $data['package'] != $data['current']) {
		die ('Invalid package!');
	}
	$data['package'] = $parameters['package'];

	$data['classes'] = docs_cache ('classes', $parameters['package']);
	if (! is_array ($data['classes'])) {
		$data['classes'] = docs_cache_store (Docs::classes ($parameters['package']), 'classes', $parameters['package']);
	}

	$data['functions'] = docs_cache ('functions', $parameters['package']);
	if (! is_array ($data['functions'])) {
		$data['functions'] = docs_cache_store (Docs::functions ($parameters['package']), 'functions', $parameters['package']);
	}

	if ($parameters['class']) {
		if (! Docs::isClass ($parameters['class'], $data['classes'])) {
			die ('Invalid class!');
		}
		$data['class'] = $parameters['class'];
		if ($parameters['class'] == '_functions_') {
			$functions = docs_cache ('func_data', $parameters['package']);
			if (! is_array ($functions)) {
				$functions = docs_cache_store (Docs::getFunctions ($parameters['package']), 'func_data', $parameters['package']);
			}
			template_simple_register ('functions', $functions);
		} else {
			$class = docs_cache ('class_data', $parameters['package'], $parameters['class']);
			if (! is_array ($class)) {
				$class = docs_cache_store (Docs::getClass ($parameters['package'], $parameters['class']), 'class_data', $parameters['package'], $parameters['class']);
			}
			template_simple_register ('class', $class);
		}
	}
} else {
	$data['package'] = false;
	$data['class'] = false;
}

$data['apps'] = Docs::apps ();
$data['current'] = $parameters['current'];
$data['current_name'] = $data['apps'][$data['current']];
if (empty ($data['current_name'])) {
	$data['current_name'] = appconf ('title');
}

page_add_style (site_prefix () . '/inc/app/docs/html/docs.css');
//page_add_script (site_prefix () . '/js/jquery-1.2.3.min.js');
page_add_script (site_prefix () . '/js/jquery.cookie.js');
page_add_script (site_prefix () . '/js/jquery-treeview/jquery.treeview.min.js');
page_add_script (site_prefix () . '/js/jquery-treeview/jquery.treeview.async.js');
page_add_style (site_prefix () . '/js/jquery-treeview/jquery.treeview.css');
page_add_script ('<script type="text/javascript">

$(document).ready (function () {
	$("#packages").treeview ({
		animated: "medium",
		control: "#sidetreecontrol",
		persist: "location",
		//url: "' . site_prefix () . '/index/docs-menu-action"
	});
});

</script>');

page_head_title (appconf ('title'));

echo template_simple ('index.spt', $data);

//info ($data);

?>
