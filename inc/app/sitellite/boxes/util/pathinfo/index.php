<?php

global $site, $conf;

page_title (intl_get ('Path Info'));

echo '<h2>$conf[\'Site\']</h2>';
echo '<pre>';
print_r ($conf['Site']);
echo '</pre>';

echo '<h2>$site</h2>';
echo '<pre>';
print_r ($site);
echo '</pre>';

?>