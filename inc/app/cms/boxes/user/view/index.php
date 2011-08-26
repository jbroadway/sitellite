<?php

$info = session_get_user ($parameters['user']);

if (! $info) {
	page_title (intl_get ('Viewing User'));
	echo '<p>' . intl_get ('Error') . ': ' . intl_get ('User not found.') . '</p>';
}

if (empty ($info->lastname)) {
	$name = $info->username;
} else {
	$name = $info->lastname . ', ' . $info->firstname;
}

page_title (intl_get ('Viewing User') . ': ' . $name);

echo template_simple ('user/view.spt', $info);

//info ($info);

?>