<?php

class Bitly {
	var $login = 'sitellite';
	var $apiKey = 'R_0f8ca66a321bbe7715e73dce48759449';
	function shorten ($url) {
		$post = sprintf (
			'http://api.bit.ly/shorten?version=2.0.1&longUrl=%s&login=%s&apiKey=%s&format=json&history=1',
			urlencode ($url),
			$this->login,
			$this->apiKey
		);
		$curl = curl_init ();
		curl_setopt ($curl, CURLOPT_URL, $post);
		curl_setopt ($curl, CURLOPT_HEADER, false);
		curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
		$res = curl_exec ($curl);
		curl_close ($curl);
		$obj = json_decode ($res, true);
		return $obj['results'][$url]['shortUrl'];
	}
}

?>