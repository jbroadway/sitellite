<?php

/**
 * Implements a basic HTTP (extendable to other protocols as well) request
 * and request parsing package.  Uses the fsockopen() function as a basis
 * for making requests.
 *
 * @package SiteSearch
 */
class Fetch {
	/**
	 * User agent to use when making requests.
	 */
	var $ua = 'Fetch/1.0 (Sitellite CMS)';

	/**
	 * Default content type when none is provided in the response headers.
	 */
	var $defaultContentType = 'text/html';

	/**
	 * Default port when none is provided in the get() URL requests.
	 */
	var $defaultPort = 80;

	/**
	 * Number of seconds to wait before a request should give up.
	 */
	var $timeout = 30;

	/**
	 * Error code returned if fsockopen() fails.
	 */
	var $errno = false;

	/**
	 * Error message should an error occur anywhere in this package.
	 */
	var $error = false;

	/**
	 * Constructor method.
	 *
	 * @param string
	 */
	function Fetch ($ua = false) {
		if ($ua) {
			$this->ua = $ua;
		}
	}

	/**
	 * Parses the specified URL using the parse_url() function, but also
	 * ensures that the scheme value is set to 'http' if none was present
	 * in the URL, and also that the port value is set to $defaultPort if
	 * none was present.
	 *
	 * @param string
	 * @return array hash
	 */
	function parseUrl ($url) {
		$r = parse_url ($url);
		if (! isset ($r['scheme'])) {
			$r['scheme'] = 'http';
		}
		if (! isset ($r['port'])) {
			$r['port'] = $this->defaultPort;
		}
		return $r;
	}

	/**
	 * Retrieves the specified URL.  Returns the entire un-parsed response.
	 *
	 * @param string
	 * @return string
	 */
	function get ($url) {
		$r = $this->parseUrl ($url);
		if ($r['scheme'] != 'http') {
			$host = $r['scheme'] . '://' . $r['host'];
			return $this->getScheme ($r['scheme'], $host, $r);
		} else {
			$host = $r['host'];
		}

		$f = @fsockopen ($host, $r['port'], $this->errno, $this->error, $this->timeout);
		if (! $f) {
			return false;
		} else {
			fputs ($f, sprintf ("GET %s HTTP/1.0\r\nHost: %s\r\nUser-Agent: %s\r\n\r\n", $r['path'] . '?' . $r['query'], $r['host'], $this->ua));
			$data = '';
			while (! feof ($f)) {
				$data .= fgets ($f, 128);
			}
			fclose ($f);
		}
		return $data;
	}

	/**
	 * Override this function to implement alternate scheme handlers.
	 * Please note that the $host parameter is prefixed by the scheme
	 * and '://' already.  To access the host value independently, use
	 * $parsed['host'].
	 *
	 * @param string
	 * @param string
	 * @param array hash from parseUrl()
	 * @return string
	 */
	function getScheme ($scheme, $host, $parsed) {
		return '';
	}

	/**
	 * Returns an array containing the response headers and body separated.
	 *
	 * @param string response string
	 * @return array
	 */
	function splitRequest ($data) {
		return explode ("\r\n\r\n", $data, 2);
	}

	/**
	 * Returns the content type from a response header string.
	 * If no content type is found, it returns the value of the
	 * $defaultContentType property.
	 *
	 * @param string response headers
	 * @return string
	 */
	function getContentType ($data) {
		if (preg_match ('/Content-Type: ([^\r\n\t ]+)\r\n/i', $data . "\r\n", $regs)) {
			return $regs[1];
		}
		// unknown, sane default
		return $this->defaultContentType;
	}

	/**
	 * Returns the response code and message from the header string.
	 * The response is an array with the first value being the response
	 * code, and the second being the message.
	 *
	 * @param string response headers
	 * @return array
	 */
	function getResponseCode ($data) {
		if (preg_match ('/HTTP\/[0-9\.]+ ([0-9]+) ([^\r\n\t ]+)\r\n/i', $data . "\r\n", $regs)) {
			return array ($regs[1], $regs[2]);
		}
		return array (200, 'OK');
	}

	/**
	 * Determines whether the response is a redirect.  If so,
	 * it returns the HTTP Location value.  If not, it returns
	 * false.
	 *
	 * @param string response headers
	 * @return mixed
	 */
	function isRedirect ($data) {
		if (preg_match ('/Location: ([^\r\n\t ]+)\r\n/i', $data . "\r\n", $regs)) {
			return $regs[1];
		}
		return false;
	}

	/**
	 * Merges the $url and $redirect into one $request url then returns
	 * an array with the new $request url as the first value, and the
	 * second being a call to $this->get ($request).
	 *
	 * @param string
	 * @param string
	 * @return array
	 */
	function getRedirect ($url, $redirect) {
		// merge $url and $redirect into one $request url then return get($request)
		$request = '';

		// url is current request
		if (strstr ($redirect, '://')) {
			$request = $redirect; // full url
		} elseif (strpos ($redirect, '/') === 0) {
			$info = parse_url ($url);
			$request = $info['scheme'] . '://' . $info['host'] . $redirect;
		} else {
			$info = pathinfo ($url);
			$request = $info['dirname'] . '/' . $redirect;
		}

		return array ($request, $this->get ($request));
	}

	/**
	 * Returns a parsed HTTP request for the specified $url.
	 * The request array has the following structure:
	 *
	 * array (
	 *     url                     => string
	 *     headers                 => string
	 *     body                    => string
	 *     response-code           => integer
	 *     response-code-message   => string
	 *     content-type            => string
	 * )
	 *
	 * @param string
	 * @return array hash
	 */
	function getParsed ($url) {
		$response = $this->get ($url);

		if (! $response) {
			return false;
		}

		$data = array ();

		list ($data['headers'], $data['body']) = $this->splitRequest ($response);

		$redirect = $this->isRedirect ($data['headers']);

		while ($redirect) {
			list ($url, $response) = $this->getRedirect ($url, $redirect);
			list ($data['headers'], $data['body']) = $this->splitRequest ($response);
			$redirect = $this->isRedirect ($url, $data['headers']);
		}

		list ($data['response-code'], $data['response-code-message']) = $this->getResponseCode ($data['headers']);

		$data['content-type'] = $this->getContentType ($data['headers']);

		$data['url'] = $url;

		return $data;
	}
}

?>