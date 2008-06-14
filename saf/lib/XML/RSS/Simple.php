<?php

loader_import ('saf.Database.PropertySet');
loader_import ('saf.XML.Sloppy');

/**
 * As the name suggests, this is a simple class for fetching RSS data
 * from remote sources.  It consists, so far, of a single method,
 * fetch(), which retrieves the source, and handles caching for you
 * as well, using the saf.Database.PropertySet package for doing so
 * (a simple but effective cache).
 *
 * If you require post-rendering caching, that's for you to handle.
 *
 * @access	public
 * @package	XML
 */
class SimpleRSS {
	/**
	 * Fetches a remote XML document and returns an object structure
	 * parsed by SloppyDOM.  Returns false if there is a parsing
	 * error, and sets the $error property with the error message.
	 * $expires can be set to either 'auto' (the default), which
	 * tries to discover the cache duration based on the
	 * syn:updatePeriod and syn:updateFrequency values in the
	 * feed itself, and defaults to 1 hour (3600 seconds) if they
	 * are not present.  If $expires is set to a number, that is
	 * used as the number of seconds to cache the feed for.
	 *
	 * @access	public
	 * @param	string
	 * @param	mixed
	 * @return	object
	 */
	function &fetch ($url, $expires = 'auto') {
		$ps = new PropertySet ('rss_fetch', 'source');

		$doc = false;
		$res = $ps->get ($url);
		if ($res) {
			$doc = unserialize ($res);
			if ($doc->_expires < time ()) {
				$doc = false;
			}
		}

		if (! $doc) {
			if (extension_loaded ('curl')) {
				$ch = curl_init ();
				curl_setopt ($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_MAXREDIRS, 3);
				curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($ch, CURLOPT_VERBOSE, 0);
				curl_setopt ($ch, CURLOPT_HEADER, 0);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);
				$rssfeed = curl_exec ($ch);
				if (! $rssfeed) {
					$this->error = 'RSS source not found: ' . curl_error ($ch) . ' (' . curl_errno ($ch) . ')';
					curl_close ($ch);
					return false;
				}
				curl_close ($ch);
			} else {
				$rssfeed = @file ($url);
				if (! is_array ($rssfeed) || count ($rssfeed) <= 0) {
					$this->error = 'RSS source not found';
					return false;
				}
				$rssfeed = @join ('', $rssfeed);
			}
				
			$sloppy = new SloppyDOM;

			$doc = $sloppy->parse ($rssfeed);
			if (! $doc) {
				$this->error = $sloppy->error;
				return false;
			}

			$root = $doc->root->name;

			$ns = false;
			foreach (array_keys ($doc->root->attributes) as $k) {
				if ($doc->root->attributes[$k]->value == 'http://purl.org/rss/1.0/modules/syndication/') {
					$ns = str_replace ('xmlns:', '', $doc->root->attributes[$k]->name);
				}
			}
			if (! $ns) {
				$ns = 'syn';
			}

			// auto tries to auto-discover the cache expiration time
			if ($expires == 'auto') {
				$node =& $doc->query ($root . '/channel/' . $ns . ':updatePeriod');
				if (is_object ($node)) {
					switch ($node->content) {
						case 'yearly':
							$node =& $doc->query ($root . '/channel/' . $ns . ':updateFrequency');
							$expires = $node->content * 31536000;
							break;
						case 'monthly':
							$node =& $doc->query ($root . '/channel/' . $ns . ':updateFrequency');
							$expires = $node->content * 2592000;
							break;
						case 'weekly':
							$node =& $doc->query ($root . '/channel/' . $ns . ':updateFrequency');
							$expires = $node->content * 604800;
							break;
						case 'daily':
							$node =& $doc->query ($root . '/channel/' . $ns . ':updateFrequency');
							$expires = $node->content * 86400;
							break;
						case 'hourly':
						default:
							$node =& $doc->query ($root . '/channel/' . $ns . ':updateFrequency');
							$expires = $node->content * 3600;
							break;
					}
				} else {
					// if all else fails, default to 1 hour
					$expires = 3600;
				}

			// require an expiry time -- can't be none
			} elseif ($expires <= 0) {
				$expires = 3600;
			}

			$doc->_expires = time () + $expires;
			$ps->set ($url, serialize ($doc));
		}

		return $doc;
	}
}

?>