<?php

loader_import ('saf.Date');

function news_shortdate ($date) {
	return Date::format ($date, appconf ('shortdate'));
}

function news_date ($date) {
	return Date::format ($date, appconf ('date'));
}

function news_time ($time) {
	return Date::time ($time, appconf ('time'));
}

function news_date_comments ($time) {
	return Date::timestamp ($time, appconf ('date_time'));
}

function news_comment_body ($body) {
	return preg_replace (
		'|(http://[^\r\n\t ]+)|is',
		'<a href="\1" target="_blank">\1</a>',
		str_replace (
			NEWLINE,
			'<br />' . NEWLINE,
			htmlentities_compat ($body)
		)
	);
}

function news_comment_body_email ($body) {
	return htmlentities_compat ($body);
}

function news_page_split ($body) {
	return preg_split ('/<hr[^>]*>/is', $body);
}

function news_page_nav ($pages, $pagenum, $story, $highlight = '') {
	if (count ($pages) == 0) {
		return '';
	}
	if (count ($pages) == 1) {
		return array_shift ($pages);
	}

	switch (appconf ('page_nav_style')) {
		case 'pager':
			// build links
			$links = '<p class="news-page-nav">' . intl_get ('Page') . ':';
			if (! empty ($highlight)) {
				$highlight = '?highlight=' . urlencode ($highlight);
			}
			if ($pagenum > 1) {
				$links .= ' &nbsp; <a href="' . site_prefix () . '/index/news-app/story.' . $story . '/pagenum.' . ($pagenum - 1) . '">' . intl_get ('Previous') . '</a>';
			} else {
				$links .= ' &nbsp; <span class="news-page-nav-inactive">' . intl_get ('Previous') . '</span>';
			}
			for ($i = 0; $i < count ($pages); $i++) {
				if ($pagenum == ($i + 1)) {
					$links .= ' &nbsp; <strong class="news-page-nav-current">' . $pagenum . '</strong>';
				} else {
					$links .= ' &nbsp; <a href="' . site_prefix () . '/index/news-app/story.' . $story . '/pagenum.' . ($i + 1) . $highlight . '">' . ($i + 1) . '</a>';
				}
			}
			if ($pagenum != 'all' && $pagenum < count ($pages)) {
				$links .= ' &nbsp; <a href="' . site_prefix () . '/index/news-app/story.' . $story . '/pagenum.' . ($pagenum + 1) . '">' . intl_get ('Next') . '</a>';
			} else {
				$links .= ' &nbsp; <span class="news-page-nav-inactive">' . intl_get ('Next') . '</span>';
			}
			if ($pagenum == 'all') {
				$links .= ' &nbsp; <strong class="news-page-nav-current">' . intl_get ('All') . '</strong>';
			} else {
				$links .= ' &nbsp; <a href="' . site_prefix () . '/index/news-app/story.' . $story . '/pagenum.all">' . intl_get ('All') . '</a>';
			}
			$links .= '</p>';
			break;

		case 'headers':
			$links = '';
			$op = '';
			foreach ($pages as $k => $page) {
				if ($k + 1 == $pagenum) {
					if (preg_match ('|<(h[0-6]).*?' . '>([^<]+)</\1>|is', $page, $regs)) {
						$links .= $op . intl_get ('Page') . ' ' . ($k + 1) . ': ' . $regs[2] . '';
						$op = "<br />\n";
					} else {
						$text = trim (strip_tags ($page));
						$links .= $op . intl_get ('Page') . ' ' . ($k + 1) . ': ' . substr ($text, 0, 32) . '...';
						$op = "<br />\n";
					}
				} else {
					if (preg_match ('|<(h[0-6]).*?' . '>([^<]+)</\1>|is', $page, $regs)) {
						$links .= $op . intl_get ('Page') . ' ' . ($k + 1) . ': <a href="' . site_prefix () . '/index/news-app/story.' . $story . '/pagenum.' . ($k + 1) . '">' . $regs[2] . '</a>';
						$op = "<br />\n";
					} else {
						$text = trim (strip_tags ($page));
						$links .= $op . intl_get ('Page') . ' ' . ($k + 1) . ': <a href="' . site_prefix () . '/index/news-app/story.' . $story . '/pagenum.' . ($k + 1) . '">' . substr ($text, 0, 32) . '...</a>';
						$op = "<br />\n";
					}
				}
			}
			if (! empty ($links)) {
				$links = '<p class="news-page-nav">' . $links . '</p>';
			}
			break;

		case 'prev-next':
			$links = '';
			if (isset ($pages[$pagenum - 2])) {
				$prev = $pages[$pagenum - 2];
				if (preg_match ('|<(h[0-6]).*?' . '>([^<]+)</\1>|is', $prev, $regs)) {
					$links .= intl_get ('Previous') . ': <a href="' . site_prefix () . '/index/news-app/story.' . $story . '/pagenum.' . ($pagenum - 1) . '">' . $regs[2] . '</a>';
				} else {
					$text = trim (strip_tags ($prev));
					$links .= intl_get ('Previous') . ': <a href="' . site_prefix () . '/index/news-app/story.' . $story . '/pagenum.' . ($pagenum - 1) . '">' . substr ($text, 0, 32) . '...</a>';
					$op = "<br />\n";
				}
			}
			if (isset ($pages[$pagenum])) {
				$next = $pages[$pagenum];
				if (preg_match ('|<(h[0-6]).*?' . '>([^<]+)</\1>|is', $next, $regs)) {
					if (! empty ($links)) {
						$links .= "<br />\n";
					}
					$links .= intl_get ('Next') . ': <a href="' . site_prefix () . '/index/news-app/story.' . $story . '/pagenum.' . ($pagenum + 1) . '">' . $regs[2] . '</a>';
				} else {
					if (! empty ($links)) {
						$links .= "<br />\n";
					}
					$text = trim (strip_tags ($next));
					$links .= intl_get ('Next') . ': <a href="' . site_prefix () . '/index/news-app/story.' . $story . '/pagenum.' . ($pagenum + 1) . '">' . substr ($text, 0, 32) . '...</a>';
					$op = "<br />\n";
				}
			}
			if (! empty ($links)) {
				$links = '<p class="news-page-nav">' . $links . '</p>';
			}
			break;

		default:
			$links = '';
	}

	// remove leading/trailing breaks from body
	$body = "";
	foreach (array_keys ($pages) as $n) {
		$pages[$n] = preg_replace ('/^([\r\n\t ]*<br[^>]*>[\r\n\t ]*)*/is', '', $pages[$n]);
		$pages[$n] = preg_replace ('/([\r\n\t ]*<br[^>]*>[\r\n\t ]*)*$/is', '', $pages[$n]);
		$body .= NEWLINE;
		if ($pagenum == 'all') {
			$body .= '<div class="news-split" id="news-page' . ($n + 1) . '">' . $pages[$n] . '</div>';
			if ($n != count ($pages) - 1) {
				$body .= '<p class="news-spacer"></p>';
			}
		} elseif ($n == $pagenum - 1) {
			$body .= '<div class="news-split" id="news-page' . ($n + 1) . '">' . $pages[$n] . '</div>';
		} else {
			$body .= '<div class="news-hidden news-split" id="news-page' . ($n + 1) . '">' . $pages[$n] . '</div>';
		}
	}

	switch (appconf ('page_nav_location')) {
		case 'top':
			return $links . $body . '<p class="news-spacer"></p>';
		case 'bottom':
			return $body . '<p class="news-spacer"></p>' . $links;
		case 'both':
		default:
			return $links . $body . '<p class="news-spacer"></p>' . $links;
	}
}

function news_highlight ($text) {
	global $cgi;
	return preg_replace ('/(' . preg_quote ($cgi->query, '/') . ')/i', '<strong>\1</strong>', htmlentities_compat ($text));
}

function news_get_categories () {
	$list = array ();
	foreach (db_fetch_array ('select * from sitellite_news_category') as $row) {
		$list[] = $row->name;
	}
	return $list;
}

function news_timezone ($offset) {
	$out = $offset[0];
	$offset = substr ($offset, 1);
	$h = floor ($offset / 3600);
	$m = floor (($offset % 3600) / 60);
	return $out . str_pad ($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad ($m, 2, '0', STR_PAD_LEFT);
}

function news_link_title ($t) {
	return strtolower (
		preg_replace (
			'/[^a-zA-Z0-9]+/',
			'-',
			$t
		)
	);
}

?>
