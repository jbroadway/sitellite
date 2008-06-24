<?php

if (! ini_get ('allow_url_fopen') && ! extension_loaded ('curl')) {
	die ('allow_url_fopen not available, cURL alternative also missing!');
}

class Ysearch {
	var $url = 'http://api.search.yahoo.com/WebSearchService/V1/webSearch?appid=%s&query=%s&results=%d&output=php';
	var $appid = '';
	var $site = '';
	var $results = 25;
	var $offset = 0;
	var $errno = false;
	var $error = false;

	function Ysearch ($appid, $site = false) {
		$this->appid = $appid;
		if (! $site) {
			$this->site = site_domain ();
		} else {
			$this->site = $site;
		}
	}

	function query ($q, $offset = 0) {
		// determine site list
		$s = '';
		if (is_array ($this->site)) {
			foreach ($this->site as $site) {
				$s .= '&site=' . $site;
			}
		} else {
			$s .= '&site=' . $this->site;
		}

		// add the offset
		if ($offset > 0) {
			$s .= '&start=' . ($offset + 1);
		}

		// build the request
		$request = sprintf ($this->url, $this->appid, urlencode ($q), $this->results) . $s;

		// send the request
		$results = $this->_send_and_receive ($request);
		if ($results == false) {
			$this->error = 'Web services request failed';
			return false;
		}

		// parse the results
		return $this->_parse_results ($results);
	}

	function _send_and_receive ($url) {
		if ($this->curl) {
			$ch = curl_init ();
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec ($ch);
			if (curl_errno ($ch)) {
				$this->errno = curl_errno ($ch);
				$this->error = curl_error ($ch);
				return false;
			}
			curl_close ($ch);
			return $data;
		}
		return file_get_contents ($url);
	}

	function _parse_results ($results) {
		return unserialize ($results);
	}
}

?>