<?php

page_title ('Workflow Services');

if (! is_writeable ('inc/app/cms/conf/services')) {
	echo '<p class="invalid">' . intl_get ('Warning: The workflow folder is not writeable.  Please verify that the folder \'inc/app/cms/conf/services\' and all files below it are writeable by the web server user.') . '</p>';
	return;
}

global $cgi;

loader_import ('cms.Workflow');

$apps = Workflow::getApps ();
foreach (array_keys ($apps) as $k) {
	$apps[$apps[$k]] = Workflow::getServices ($k);
	unset ($apps[$k]);
}

if (empty ($cgi->submit_button)) {
	echo template_simple ('workflow.spt', $apps);
} else {

	$services = array ();

	foreach ($apps as $app) {
		foreach ($app as $service) {
			if ($parameters[$service['name']]) {
				foreach ($service['actions'] as $action) {
					if (! is_array ($services[$action])) {
						$services[$action] = array ();
					}
					$services[$action]['service:' . $service['name']] = array (
						'name' => $service['title'],
						'handler' => $service['handler'],
					);
				}
			}
		}
	}

	loader_import ('saf.File');
	loader_import ('saf.File.Directory');

	foreach ($services as $action => $servs) {
		file_overwrite ('inc/app/cms/conf/services/' . $action . '.php', ini_write ($servs));
	}

	foreach (Dir::find ('*.php', 'inc/app/cms/conf/services') as $file) {
		if (! isset ($services[str_replace ('.php', '', basename ($file))])) {
			file_overwrite ($file, ini_write (array ()));
		}
	}

	echo '<p>' . intl_get ('Workflow settings saved.') . '  <a href="' . site_prefix () . '/index/cms-cpanel-action">' . intl_get ('Continue') . '</a></p>';
}

?>