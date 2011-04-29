<ul><?php

foreach (db_shift_array ('select distinct id from sitewiki_page where id != "" order by id asc') as $page) {
	printf (
		'<li><a href="%s/index/sitewiki-app/show.%s">%s</a></li>',
		site_prefix (),
		$page,
		$page
	);
}

?></ul>
