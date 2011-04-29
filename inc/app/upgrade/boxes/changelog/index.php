<?php

page_title (intl_get ('Change Log'));

echo '<p><a href="' . site_prefix () . '/index/upgrade-app">' . intl_get ('Back') . '</a></p>';

echo '<p>' . nl2br (htmlentities_compat (file_get_contents ('install/changes.txt'))) . '</p>';

?>