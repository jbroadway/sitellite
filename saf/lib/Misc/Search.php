<?php

ini_set ('iconv.input_encoding', 'utf-8');
ini_set ('iconv.internal_encoding', 'utf-8');
ini_set ('iconv.output_encoding', 'utf-8');

/**
 * Splits a search into an array of individual terms.  Properly handles
 * quoted strings as well, keeping them as a single literal term.
 *
 * @param string
 * @return array
 * @package Misc
 */
function search_split_query ($query) {
	$query = stripslashes ($query);
	$pieces = array ();
	$res = preg_split ('/("|\+|-| +|\t|\r|\n)/', $query, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	$open = false;
	foreach ($res as $piece) {
		//echo '--' . htmlentities ($piece) . '--' . BR;
		if ($open && $piece == '"') {
			$open = false;
		} elseif ($open) {
			$pieces[count ($pieces) - 1] .= $piece;
		} elseif ($piece == '"') {
			$open = true;
			$pieces[] = '';
		} elseif (preg_match ('/^[ \t\r\n`~!@#$%^&*()_\\=+\|\/\[\]{}:;<>.?]+/', $piece)) {
			continue;
		//} elseif (preg_match ('/[\wa-zA-Z0-9_\'\.\/"-]/ui', $piece)) {
		//	$pieces[] = $piece;
		} else {
			$pieces[] = $piece;
		}
	}
	return $pieces;
}

/**
 * Highlights all of the query terms in the specified string, wrapping
 * them in <span class="highlighted"></span> tags.
 * Source: http://www.ilovejackdaniels.com/php/google-style-keyword-highlighting/
 *
 * @param string
 * @param array
 * @return string
 * @package Misc
 */
function search_highlight ($string, $queries) {

	if (! is_array ($queries)) {
		$queries = search_split_query ($queries);
	}

	// A max of ten search terms
	$j = (sizeof($queries) > 10) ? 10 : sizeof($queries);

	// There are search terms, highlight these
	if ($j > 0) {

		for ($i = 0; $i < $j; $i++) {
			//$string = preg_replace('/(>)([^<]*)([^a-z]+)(' . $queries[$i] . ')([^a-z]+)/i', '$1$2$3<span style="font-weight: bold; background-color: yellow;">$4</span>$5', $string);
			$string = preg_replace('#(\>(((?' . '>([^><]+|(?R)))*)\<))#use', "preg_replace('#(" . str_replace("'", "", $queries[$i]) . ")#usi', '<span class=\"highlighted\">\\\\1</span>', '\\0')", '>' . $string . '<');
			if (function_exists ('iconv_substr')) {
				$string = @iconv_substr ($string, 1, -1);
			} else {
				$string = substr ($string, 1, -1);
			}
			$string = str_replace ('\"', '"', $string);
		}
		return $string;
	}
	// Nothing to highlight
	return $string;
}

/**
 * Returns a bar that displays "Highlighting Search Terms: a, b, c <Search Again>".
 *
 * @param string
 * @param string
 * @return string
 * @package Misc
 */
function search_bar ($query, $url = '/index/sitellite-search-action') {
	return '
	<p><table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td style="background-color: #feb; padding: 3px; margin-top: 20px; color: #444; border-bottom: 1px solid #777">
				' . intl_get ('Highlighting Search Terms') . ': <strong>' . htmlentities_compat ($query) . '</strong>
			</td>

			<td align="right" style="background-color: #feb; padding: 3px; margin-top: 20px; text-align: right; border-bottom: 1px solid #777">
				<a style="color: #444;" href="' . site_prefix () . $url . '">' . intl_get ('Search Again') . '</a>
			</td>
		</tr>
	</table></p>' . NEWLINEx2;
}

/**
 * Check if the previous request was a search engine results page. If
 * so, then parse the search keywords.
 *
 * @param string Referer URL
 * @return array Search terms
 * @package Misc
 */
function get_searchengine_keywords ($referer) {

	$keywords = "";
	$url = urldecode($referer);
		
	// Google
	if (preg_match("/www\.google/i",$url)) { 
		preg_match("'(\?|&)q=(.*?)(&|$)'si", " $url ", $keywords);
	}
	// AllTheWeb
	if (preg_match("/www\.alltheweb/i",$url)) { 
		preg_match("'(\?|&)q=(.*?)(&|$)'si", " $url ", $keywords); 
	}
	// MSN
	if (preg_match("/search\.msn/i",$url)) { 
		preg_match("'(\?|&)q=(.*?)(&|$)'si", " $url ", $keywords); 
	}
	// Yahoo 
	if ((preg_match("/yahoo\.com/i",$url)) or (preg_match("/search\.yahoo/i",$url))) { 
		preg_match("'(\?|&)p=(.*?)(&|$)'si", " $url ", $keywords); 
	} 
	// Looksmart 
	if (preg_match("/looksmart\.com/i",$url)) { 
		preg_match("'(\?|&)qt=(.*?)(&|$)'si", " $url ", $keywords); 
	}
	// Ilse.nl
	if (preg_match("/ilse\.nl/i",$url)) { 
		preg_match("'(\?|&)search_for=(.*?)(&|$)'si", " $url ", $keywords); 
	}
	// Vinden.nl or zoeken.nl
	if (preg_match("/(vinden\.nl|zoeken\.nl)/i",$url)) { 
		preg_match("'(\?|&)query=(.*?)(&|$)'si", " $url ", $keywords); 
	}
	
	if (($keywords[2] != '') and ($keywords[2] != ' ')) {
		$keywords = preg_replace('/"|\'/', '', $keywords[2]); // Remove quotes
		return preg_split("/[\s,\+\.]+/",$keywords); // Create keyword array
	}
	return false;
}

/**
 * If you would like search terms to be automatically highlighted in your
 * web pages, add 'filter 1 = "body: saf.Misc.Search"' to the modes.php file
 * in your template set, under the appropriate output mode.
 *
 * @param string
 * @return string
 * @package Misc
 */
function saf_misc_search_content_filter ($body) {
	global $cgi;
	if (! empty ($cgi->highlight)) {
		return search_bar ($cgi->highlight) . search_highlight ($body, $cgi->highlight);
	}
	// Was the previous page a search results page of a known search engine?
	// If so, then highlight the search keywords also.
	else if ((isset($_SERVER['HTTP_REFERER'])) and ($_SERVER['HTTP_REFERER'] != '')) { 
		$keywords = get_searchengine_keywords ($_SERVER['HTTP_REFERER']);
		if ($keywords) { 
			return search_bar (implode(' ', $keywords)) . search_highlight ($body, $keywords);
		}
	}
	return $body;
}

?>