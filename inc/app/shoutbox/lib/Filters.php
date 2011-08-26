<?php

loader_import ('saf.Date');

function shoutbox_filter_date ($date) {
    return Date::format ($date, 'M j, Y - g:i A');
}

?>