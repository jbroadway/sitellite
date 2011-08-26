<?php

/**
 * If you would like glossary terms to be automatically highlighted in your
 * web pages, add "siteglossary.Terms" to the [Server][content_filters] line
 * in inc/conf/config.ini.php.
 */
function siteglossary_terms_content_filter ($body) {
	if (strpos ($_SERVER['REQUEST_URI'], '/index/siteglossary-app') === false) {
		$terms = db_fetch_array ('select * from siteglossary_term');
		foreach (array_keys ($terms) as $k) {
			$body = str_replace('\"', '"', substr(preg_replace('#(\>(((?' . '>([^><]+|(?R)))*)\<))#se', "preg_replace('#\b(" . str_replace("'", "", $terms[$k]->word) . ")\b#i', '<a href=\"" . site_prefix () . "/index/siteglossary-app#" . $terms[$k]->word . "\" class=\"glossary-term\" title=\"" . str_replace ('"', '\\"', $terms[$k]->description) . "\">\\\\1</a>', '\\0')", '>' . $body . '<'), 1, -1));
		}
	}
	return $body;
}

?>