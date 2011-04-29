<?php

// BEGIN CLI KEEPOUT CHECKING
if (php_sapi_name () !== 'cli') {
    // Add these lines to the very top of any file you don't want people to
    // be able to access directly.
    header ('HTTP/1.1 404 Not Found');
    echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
        . "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
        . "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
        . $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
    exit;
}
// END CLI KEEPOUT CHECKING

require_once ('class.jabber.php');

$j = new Jabber;
$j->server = false;
$j->enable_logging = true;

function jerr (&$j) {
	return $j->log_array[count ($j->log_array) - 1];
}

$t = true;

while ($t) {
	echo ($j->server) ? $j->server . '> ' : 'jabber> ';
	$input = explode (' ', trim (fgets (STDIN)));

	switch ($input[0]) {
		case '':
			break;

		case 'connect':
			if (! $input[1]) {
				echo "Error: hostname required.\n";
				break;
			}

			list ($user, $server) = explode ('@', $input[1]);
			if (! $server) {
				$server = $user;
				$user = false;
			}

			$j->server = $server;

			if (! $input[2]) {
				$input[2] = 5222;
			}
			$j->port = $input[2];

			if (! $j->Connect ()) {
				echo "Error: connection failed.  " . jerr ($j) . "\n";
				$j->server = false;
				break;
			}

			if (! $user) {
				echo 'username: ';
				$u = trim (fgets (STDIN));
				$j->username = $u;
			} else {
				$j->username = $user;
			}

			echo 'password: ';
			system ('stty -echo');
			$p = trim (fgets (STDIN));
			system ('stty echo');
			$j->password = $p;
			echo "\n";

			if (! $j->SendAuth ()) {
				echo "Error: authentication failed.  " . jerr ($j) . "\n";
				$j->server = false;
			} else {
				echo "Connected to server " . $j->server . ".\n";
			}
			break;

		case 'register':
			if (! $input[1]) {
				echo "Error: hostname required.\n";
				break;
			}

			list ($user, $server) = explode ('@', $input[1]);
			if (! $server) {
				$server = $user;
				$user = false;
			}

			$j->server = $server;

			if (! $input[2]) {
				$input[2] = 5222;
			}
			$j->port = $input[2];

			if (! $j->Connect ()) {
				echo "Error: connection failed.  " . jerr ($j) . "\n";
				break;
			}

			if (! $user) {
				echo 'username: ';
				$u = trim (fgets (STDIN));
				$j->username = $u;
			} else {
				$j->username = $user;
			}

			echo 'password: ';
			system ('stty -echo');
			$p = trim (fgets (STDIN));
			system ('stty echo');
			$j->password = $p;
			echo "\n";

			echo 'email: ';
			$email = trim (fgets (STDIN));
			if (! $email) {
				$email = null;
			}

			echo 'name: ';
			$name = trim (fgets (STDIN));
			if (! $name) {
				$name = null;
			}

			if (! $j->AccountRegistration ($email, $name)) {
				echo "Error: account creation failed.  " . jerr ($j) . "\n";
			} else {
				echo "Account created.\n";
			}
			break;

		case 'disconnect':
			$j->enable_logging = false;
			$j->Disconnect ();
			$j->server = '';
			break;

		case 'status':
			if (! $input[1]) {
				$input[1] = 'available';
			}
			$j->SendPresence ($input[1]);
			echo "Status sent.\n";
			break;

		case 'msg':
			$user = $input[1];
			if (! strstr ($user, '@')) {
				$user .= '@' . $j->server;
			}

			echo 'type (normal/optional, chat, groupchat, headline, error): ';
			$type = trim (fgets (STDIN));
			if (! $type) {
				$type = null;
			}

			echo 'thread (optional): ';
			$thread = trim (fgets (STDIN));
			if ($thread) {
				$msg['thread'] = $thread;
			}

			echo 'message: ';
			$message = trim (fgets (STDIN));
			$msg['body'] = $message;

			if (! $j->SendMessage ($user, $type, null, $msg)) {
				echo "Error: message failed to send.  " . jerr ($j) . "\n";
			} else {
				echo "Message sent.\n";
			}
			break;

		case 'get':
			$j->SendPresence ('available');
			$j->Listen ();
			echo count ($j->packet_queue) . " messages:\n";
			foreach (array_keys ($j->packet_queue) as $k) {
				echo '# ' . $k . ' - ' . key ($j->packet_queue[$k]) . "\n";
			}
			$num = false;
			break;

		case 'read':
			if ($input[1]) {
				$num = $input[1];
			} else {
				if ($num === false) {
					$num = 0;
				} else {
					$num++;
					if ($num >= count ($j->packet_queue)) {
						$num = 0;
					}
				}
			}

			$msg = $j->packet_queue[ (int) $num];
			$type = $j->{'GetInfoFrom' . ucfirst (strtolower (key ($msg))) . 'Type'} ($msg);
			echo 'packet type: ' . key ($msg) . "\n";
			echo 'message type: ' . $type . "\n";
			echo 'from: ' . $j->{'GetInfoFrom' . ucfirst (strtolower (key ($msg))) . 'From'} ($msg) . "\n";
			if (key ($msg) == 'message') {
				$thread = $j->{'GetInfoFrom' . ucfirst (strtolower (key ($msg))) . 'Thread'} ($msg);
				if ($thread) {
					echo 'thread: ' . $thread . "\n";
				}
			}
			echo $j->GetInfoFromMessageBody ($msg) . "\n";
			break;

		case 'dump':
			if ($input[1]) {
				$num = $input[1];
			} else {
				if ($num === false) {
					$num = 0;
				} else {
					$num++;
					if ($num >= count ($j->packet_queue)) {
						$num = 0;
					}
				}
			}

			$msg = $j->packet_queue[ (int) $num];
			var_dump ($msg);
			break;

		case 'reply':
			if (! $input[1]) {
				break;
			}
			$id = $input[1];
			$reply = array ();

			$msg = $j->packet_queue[ (int) $id];
			$to = $j->{'GetInfoFrom' . ucfirst (strtolower (key ($msg))) . 'From'} ($msg);
			echo 'to: ' . $to . "\n";
			if (key ($msg) == 'message') {
				$thread = $j->{'GetInfoFrom' . ucfirst (strtolower (key ($msg))) . 'Thread'} ($msg);
				if ($thread) {
					echo 'thread: ' . $thread . "\n";
					$reply['thread'] = $thread;
				}
			}
			echo 'message: ';
			$reply['body'] = trim (fgets (STDIN));
			if (! $j->SendMessage ($to, null, null, $reply)) {
				echo "Error: message failed to send.  " . jerr ($j) . "\n";
			} else {
				echo "Message sent.\n";
			}
			break;

		case 'info':
			var_dump ($j->{$input[1]});
			break;

		case 'quit':
			if ($j->connected) {
				$j->enable_logging = false;
				$j->Disconnect ();
			}
			echo "Goodbye.\n";
			$t = false;
			break;

		case 'help':
			echo "Command list:\n";
			echo "help                            Displays this list of commands.\n";
			echo "quit                            Disconnect and exit.\n";
			echo "connect [user@]hostname [port]  Connect to the specified host.\n";
			echo "register [user@]hostname [port] Register a new account with the specified host.\n";
			echo "disconnect                      Disconnect from the current host.\n";
			echo "status available                Update your availability status.\n";
			echo "msg user@hostname               Send a message to the specified user.\n";
			echo "get                             Checks for new messages.\n";
			echo "read [0]                        Gets the specified message from the list.\n";
			echo "dump [0]                        Outputs the raw data of the specified message.\n";
			echo "reply 0                         Replies to the specified message.\n";
			echo "info property                   Inspect a property of the PHP Jabber object.\n";
			break;

		default:
			echo "Unknown command.  Try 'help' for command list.\n";
			break;
	}
}

exit;

?>