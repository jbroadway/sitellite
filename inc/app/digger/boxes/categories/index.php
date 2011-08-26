<?php

echo '<ul>';

foreach(db_pairs('select * from digger_category order by category asc') as $k => $v) {
    echo '<li><a href="' . site_prefix() . '/index/digger-app/category.' . $k . '/name.' . $v . '">' . $v . '</a></li>' . NEWLINE;
}

echo '</ul>';

?>