<ul>
<?php

foreach (db_fetch_array ('select * from siteblog_blogroll order by weight desc, title asc') as $row) {
	echo '<li><a href="' . $row->url . '">' . $row->title . '</a></li>';
}

?>
</ul>
