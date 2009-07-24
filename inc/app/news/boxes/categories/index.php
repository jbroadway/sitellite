<ul>
<?php

$cat = db_fetch_array ('SELECT name, COUNT(id) AS num
	FROM sitellite_news_category
        LEFT JOIN sitellite_news ON category=name GROUP BY name ORDER BY name');

foreach ($cat as $c) {
	echo '<li><a href="'
		. site_prefix ()
		. '/index/news-app/section.' . $c->name . '">'
		. $c->name . ' (' . $c->num . ')</a></li>';
}

?>
</ul>
