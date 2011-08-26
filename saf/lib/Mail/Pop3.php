<?php

// POP3 Client
// Provides all POP3 functionality except the optional APOP command.
// Reference: RFC 1939 (http://www.faqs.org/rfcs/rfc1939.html)

/**
 * @package Mail
 */
class Pop3 {
	var $connection;
	var $server = 'localhost';
	var $port = 110;
	var $timeout = 15;
	var $user;
	var $pass;
	var $errno;
	var $error;
	var $log = array ();
	var $logging = false;
	var $eraseFromServer = false;

	function Pop3 ($server = 'localhost', $port = 110, $timeout = 15) {
		$this->server = $server;
		$this->port = $port;
		$this->timeout = $timeout;
	}

	function connect () {
		$this->connection = fsockopen (
			$this->server,
			$this->port,
			$this->errno,
			$this->error,
			$this->timeout
		);
		if (! $this->connection) {
			return false;
		}
		$response = $this->parseResponse ($this->getResponse ());
		if ($response['success']) {
			return true;
		}
		$this->error = $response['message'];
		return false;
	}

	function authenticate ($user, $pass) {
		$this->user = $user;
		$this->pass = $pass;

		if ($this->logging) {
			$this->log[] = "SEND: USER $user\r\n";
		}

		fputs ($this->connection, "USER $user\r\n");
		$response = $this->parseResponse ($this->getResponse ());
		if (! $response['success']) {
			$this->error = $response['message'];
			return false;
		}

		if ($this->logging) {
			$this->log[] = "SEND: PASS $pass\r\n";
		}

		fputs ($this->connection, "PASS $pass\r\n");
		$response = $this->parseResponse ($this->getResponse ());
		if (! $response['success']) {
			$this->error = $response['message'];
			return false;
		}

		return true;
	}

	function listMessages () {
		if ($this->logging) {
			$this->log[] = "SEND: LIST\r\n";
		}

		fputs ($this->connection, "LIST\r\n");

		$response = $this->parseResponse ($this->getResponse (false));

		$list = preg_split ("/\r\n/", $response['body'], -1, PREG_SPLIT_NO_EMPTY);
		$files = array ();
		foreach ($list as $file) {
			list ($num, $length) = preg_split ('/ /', $file);
			$files[$num] = array (
				'number' => $num,
				'length' => $length,
				'message' => '',
			);
		}
		return $files;
	}

	function getMessage ($number) {
		if ($this->logging) {
			$this->log[] = "SEND: RETR $number\r\n";
		}
		fputs ($this->connection, "RETR $number\r\n");
		$response = $this->parseResponse ($this->getResponse (false));

		if (! $response['success']) {
			$this->error = $response['message'];
			return false;
		}

		if ($this->eraseFromServer) {
			$this->removeMessage ($number);
		}

		return $response['body'];
	}

	function getTop ($number, $lines = 5) {
		if ($this->logging) {
			$this->log[] = "SEND: TOP $number $lines\r\n";
		}
		fputs ($this->connection, "TOP $number $lines\r\n");
		$response = $this->parseResponse ($this->getResponse (false));

		if (! $response['success']) {
			$this->error = $response['message'];
			return false;
		}

		return $response['body'];
	}

	function uniqueID ($number = '') {
		if ($this->logging) {
			$this->log[] = "SEND: UIDL $number\r\n";
		}
		if (! empty ($number)) {
			fputs ($this->connection, "UIDL $number\r\n");
			$response = $this->parseResponse ($this->getResponse ());
		} else {
			fputs ($this->connection, "UIDL\r\n");
			$response = $this->parseResponse ($this->getResponse (false));
		}

		if (! $response['success']) {
			$this->error = $response['message'];
			return false;
		}

		if (empty ($number)) {
			$list = preg_split ("/\r\n/", $response['body'], -1, PREG_SPLIT_NO_EMPTY);
			$files = array ();
			foreach ($list as $file) {
				list ($num, $length) = preg_split ('/ /', $file);
				$files[$num] = array (
					'number' => $num,
					'length' => $length,
					'message' => '',
				);
			}
			return $files;
		} else {
			list ($num, $id) = preg_split ('/ /', $response['message']);
			return $id;
		}
	}

	function removeMessage ($number) {
		if ($this->logging) {
			$this->log[] = "SEND: DELE $number\r\n";
		}

		fputs ($this->connection, "DELE $number\r\n");

		$response = $this->parseResponse ($this->getResponse ());

		if (! $response['success']) {
			$this->error = $response['message'];
			return false;
		}

		return true;
	}

	function stat () { // returns # of messages and the # of octets in the maildrop
		if ($this->logging) {
			$this->log[] = "SEND: STAT\r\n";
		}

		fputs ($this->connection, "STAT\r\n");

		$response = $this->parseResponse ($this->getResponse ());

		if (! $response['success']) {
			$this->error = $response['message'];
			return false;
		}

		return preg_split ('/ /', $response['message']);
	}

	function reset () { // resets any DELE flags on messages
		if ($this->logging) {
			$this->log[] = "SEND: RSET\r\n";
		}

		fputs ($this->connection, "RSET\r\n");

		$response = $this->parseResponse ($this->getResponse ());

		if (! $response['success']) {
			$this->error = $response['message'];
			return false;
		}

		return true;
	}

	function noop () { // just sayin' helo :)
		if ($this->logging) {
			$this->log[] = "SEND: NOOP\r\n";
		}

		fputs ($this->connection, "NOOP\r\n");

		$response = $this->parseResponse ($this->getResponse ());

		if (! $response['success']) {
			$this->error = $response['message'];
			return false;
		}

		return true;
	}

	function disconnect () {
		if ($this->logging) {
			$this->log[] = "SEND: QUIT\r\n";
		}
		fputs ($this->connection, "QUIT\r\n");
		$response = $this->getResponse ();
		fclose ($this->connection);
	}

	function getResponse ($oneliner = true) {
		$response = '';
		while ($resp = fgets ($this->connection, 128)) {
			if ($this->logging) {
				$this->log[] = 'RECV: ' . $resp;
			}
			if ($oneliner) {
				$response .= $resp;
				break;
			} elseif (strpos ($response, '-ERR') === 0 || strpos ($resp, '-ERR') === 0) {
				$response .= $resp;
				break;
			} elseif (preg_match ("/^\.(\r\n)+$/", $resp)) {
					break;
			}
			$response .= $resp;
		}
		return $response;
	}

	function parseResponse ($resp) {
		$return = array ();
		$response = preg_split ("/\r\n/", $resp, -1);
		$code = array_shift ($response);
		if (strpos ($code, '+OK') == 0) {
			$return['success'] = true;
		} elseif (strpos ($code, '-ERR') == 0) {
			$return['success'] = false;
		}
		$code = preg_replace ('/^(\+OK|-ERR) ?/i', '', $code);
		if (! empty ($code)) {
			$return['message'] = $code;
		}
		$return['body'] = join ("\r\n", $response);
		return $return;
	}
}

/*
$p = new Pop3 ('mailbox.sitellite.org', 110, 2);
$p->logging = true;
//$p->eraseFromServer = true;

if (! $p->connect ()) {
//	echo 'ERROR: ' . $p->error . ' (' . $p->errno . ')<br />';
//	echo '<pre>';
//	print_r ($p->log);
//	echo '</pre>';
} else {
//	echo "<h3>Connected!</h3>\n";
}

if (! $p->authenticate ('testing@sitellite.org', 'steak')) {
//	echo 'ERROR: ' . $p->error . '<br />';
//	echo '<pre>';
//	print_r ($p->log);
//	echo '</pre>';
} else {
//	echo "<h3>Authenticated!</h3>\n";
}

$list = $p->listMessages ();
if (! is_array ($list)) {
//	echo 'ERROR: ' . $p->error . '<br />';
//	echo '<pre>';
//	print_r ($p->log);
//	echo '</pre>';
} else {
//	echo "<h3>Got " . count ($list) . " messages!</h3>\n";
}

echo '<pre>';
foreach ($list as $key => $msg) {
	$list[$key]['body'] = $p->getMessage ($key);
	print_r ($list[$key]);
}
echo '</pre>';

//echo '<h3>RSET</h3>';
//echo '<p>' . $p->reset () . '</p>';

//echo '<h3>TOP 2 1</h3>';
//echo '<pre>' . $p->getTop (2, 1) . '</pre>';

//echo '<h3>UIDL 1</h3>';
//echo '<p>' . $p->uniqueID (1) . '</p>';

//echo '<h3>DELE 1</h3>';
//echo '<p>' . $p->removeMessage (1) . '</p>';

//echo '<h3>STAT</h3>';
//echo '<pre>';
//print_r ($p->stat ());
//echo '</pre>';

//echo '<h3>RSET</h3>';
//echo '<p>' . $p->reset () . '</p>';

//echo '<h3>STAT</h3>';
//echo '<pre>';
//print_r ($p->stat ());
//echo '</pre>';

//echo '<h3>NOOP</h3>';
//echo '<p>' . $p->noop () . '</p>';

$p->disconnect ();

echo '<h2>LOG:</h2>';
echo '<pre>';
foreach ($p->log as $line) {
	echo $line;
}
echo '</pre>';

*/

?>