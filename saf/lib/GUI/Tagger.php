<?php

/**
 * Generates a series of tag names linking to a source of data.  The names
 * are alphabetical and organized into 6 different sizes so as to visually
 * display which tags contain the most data.
 *
 * Query types:
 *
 * 1. Single table
 *
 * create table news (
 *   id
 *   title
 *   category
 *   body
 * )
 *
 * select category as tag, count(*) as count
 * from news
 * group by category asc
 *
 * Constructor:
 *
 * $tagger = new Tagger (array ('news', 'category'));
 *
 * 2. Separate category table
 *
 * create table news (
 *   id
 *   title
 *   category_id
 *   body
 * )
 *
 * create table news_category (
 *   id
 *   name
 * )
 *
 * select news_category.name as tag, count(*) as count
 * from news, news_category
 * where news.category_id = news_category.id
 * group by news_category.name asc
 *
 * Constructor:
 *
 * $tagger = new Tagger (
 *     array ('news', 'category_id'),
 *     array ('news_category', 'id', 'name')
 * );
 */

class Tagger {
	var $data_table;
	var $data_col;
	var $tag_table;
	var $tag_table_key;
	var $tag_col;

	function Tagger ($data_table, $tag_table = false) {
		$this->data_table = array_shift ($data_table);
		$this->data_col = array_shift ($data_table);
		if (! $tag_table) {
			$this->tag_table = false;
		} else {
			$this->tag_table = array_shift ($tag_table);
			$this->tag_table_key = array_shift ($tag_table);
			$this->tag_col = array_shift ($tag_table);
		}
	}

	function fetch () {
		if (! $this->tag_table) {
			$sql = sprintf (
				'select %s as tag, count(*) as count
				from %s
				group by %s asc',
				$this->data_col,
				$this->data_table,
				$this->data_col
			);
		} else {
			$sql = sprintf (
				'select b.%s as tag, count(*) as count
				from %s a, %s b
				where a.%s = b.%s
				group by b.%s asc',
				$this->tag_col,
				$this->data_table,
				$this->tag_table,
				$this->data_col,
				$this->tag_table_key,
				$this->tag_col
			);
		}

		return db_fetch_array ($sql);
	}

	function getLevel ($n, $b, $c) {
		// level is between 1 and 6

		$iter = ($c - $b) / 6;

		if ($n >= $b && $n <= floor ($b + $iter)) {
			return 1;
		}

		if ($n > floor ($b + $iter) && $n <= floor ($b + ($iter * 2))) {
			return 2;
		}

		if ($n > floor ($b + ($iter * 2)) && $n <= floor ($b + ($iter * 3))) {
			return 3;
		}

		if ($n > floor ($b + ($iter * 3)) && $n <= floor ($b + ($iter * 4))) {
			return 4;
		}

		if ($n > floor ($b + ($iter * 4)) && $n <= floor ($b + ($iter * 5))) {
			return 5;
		}

		if ($n > floor ($b + ($iter * 5)) && $n <= $c) {
			return 6;
		}
	}

	function display ($url = 0) {
		if ($url === 0) {
			$url = site_prefix () . '/index/sitesearch-app?query=%s';
		}

		$tags = $this->fetch ();

		$out = "<div class=\"tagger\">\n";
		$sep = '';
		$base = false;
		$ceil = false;

		foreach (array_keys ($tags) as $k) {
			$tag =& $tags[$k];
			if ($base === false || $tag->count < $base) {
				$base = $tag->count;
			}
			if ($ceil === false || $tag->count > $ceil) {
				$ceil = $tag->count;
			}
		}

		foreach (array_keys ($tags) as $k) {
			$tag =& $tags[$k];
			$level = $this->getLevel ($tag->count, $base, $ceil);
			if ($url !== false) {
				$href = ' href="' . sprintf ($url, $tag->tag) . '"';
			} else {
				$href = ' href="#"';
			}
			$out .= $sep . '<a' . $href . ' class="level' . $level . '" alt="' . $tag->count . ' ' . intl_get ('items') . '" title="' . $tag->count . ' ' . intl_get ('items') . '">' . $tag->tag . "</a>\n";
			$sep = ' ';
		}

		$out .= "</div>\n";

		return $out;
	}
}

/*
include_once ('../init.php');

loader_import ('saf.Database');
loader_import ('saf.Site');

$db = new Database ('MySQL:localhost:DEV', 'USER', 'PASS');
$site = new Site ($_SERVER);
$site->prefix = '';

$tagger = new Tagger (array ('sitellite_news', 'category'));

echo $tagger->display ();

echo '<style type="text/css">

div.tagger a.level1 {
	font-size: medium;
}

div.tagger a.level2 {
	font-size: large;
}

div.tagger a.level3 {
	font-size: large;
	font-weight: bold;
}

div.tagger a.level4 {
	font-size: x-large;
}

div.tagger a.level5 {
	font-size: x-large;
	font-weight: bold;
}

div.tagger a.level6 {
	font-size: xx-large;
	font-weight: bold;
}

</style>';

*/

?>