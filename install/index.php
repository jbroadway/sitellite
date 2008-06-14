<?php

include_once ('../saf/init.php');

$data = array ();

if (@file_exists ('installed')) {
	$cgi->step = 1955;
} elseif (! isset ($cgi->step)) {
	$cgi->step = 0;
}

$url = 'http://' . $_SERVER['HTTP_HOST'] . array_shift (split ('/install/', $_SERVER['REQUEST_URI']));

switch ($cgi->step) {

	case 1955:
		$data['step'] = -1;
		$data['new_site'] = '../sitellite';
		$data['title'] = 'Finished';
		$data['body'] = template_simple ('installed.spt', $data);
		break;

	case 0:
		$data['step'] = 0;
		$data['next_step'] = 1;
		$data['title'] = 'Introduction';
		$data['next'] = 'License';
		$data['body'] = '<p>Welcome to the Sitellite Content Management System installer.  The
installer will guide you through the process of installing and configuring
the Sitellite CMS on your web site.</p>

<p>Should you encounter any issues during the installation process, you may
use the official community forum to ask your questions, located at:</p>

<p><a href="http://www.sitellite.org/index/siteforum-app" target="_blank">http://www.sitellite.org/index/siteforum-app</a></p>

<p>Or you can see our commercial support options here:</p>

<p><a href="http://www.sitellite.org/index/support" target="_blank">http://www.sitellite.org/index/support</a></p>

<p>Please click "Next" to continue whenever you are ready, and on behalf of
<a href="http://www.simian.ca/" target="_blank">Simian Systems</a> and the entire <a href="http://www.sitellite.org/" target="_blank">Sitellite community</a>,
thanks for your interest in Sitellite and welcome to the neighbourhood!</p>';
		break;

	case 1:
		$data['step'] = 1;
		$data['next_step'] = 2;
		$data['title'] = 'License';
		$data['next'] = 'Requirements';
		$data['body'] = '<div style="font-size: 11px; height: 350px; width: 548px; overflow: auto; border: 1px solid #eee"><pre>' . htmlentities_compat (@join ('', @file ('inc/LICENSE'))) . '</pre></div>'
			. '<p style="float: right; padding-right: 30px; padding-top: 5px"><input type="radio" name="agree" value="yes" /> I agree<br />'
			. '<input type="radio" name="agree" value="no" checked="checked" /> I do not agree</p>';
		if ($cgi->agree == 'no') {
			$data['body'] .= '<script language="javascript" type="text/javascript">
				alert (\'You must agree to the license to continue the installation.\');
			</script>';
		}
		break;

	case 2:
		if ($cgi->agree == 'no') {
			header ('Location: ?step=1&agree=no');
			exit;
		}

		loader_import ('ext.phpsniff');
		$ua = new phpSniff ();

		if (PHP_VERSION < '4.2') {
			// requires php 4.2+
			$data['error'] = true;
			$data['body'] = '<p class="notice">Sitellite requires PHP version 4.2 or 4.3.</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>Upgrade PHP to a newer release</li></ul>';
		//} elseif (PHP_VERSION >= '5.0') {
			// requires php < 5.0
		//	$data['error'] = true;
		//	$data['body'] = '<p class="notice">Please Note: PHP5 support is still in beta, and not recommended for production use.</p>';
		} elseif (strpos (php_sapi_name (), 'apache') === false) {
			// requires apache
			$data['error'] = true;
			$data['body'] = '<p class="notice">Sitellite requires PHP to be installed as an Apache module.</p>'
				. '<h2>Solutions:</h2>'
				. '<ul><li>Recompile PHP with the flag --with-apxs</li><li>Continue the installation and follow the workaround steps for the appropriate web server <a href="http://cookbook.sitellite.org/index/sitewiki-app/show.TroubleShooting" target="_blank">found here</a></ul>';
		} elseif (! is_writeable ('../inc/conf/config.ini.php')) {
			$data['error'] = true;
			$data['body'] = '<p class="notice">Can\'t write to the Sitellite configuration file</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>In your document root, execute the command `chmod -R 0777 inc/conf`</li></ul>';
		} elseif (! is_writeable ('../inc/conf/auth')) {
			$data['error'] = true;
			$data['body'] = '<p class="notice">Can\'t write to the Sitellite inc/conf/auth directory</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>In your document root, execute the command `chmod -R 0777 inc/conf/auth`</li></ul>';
		} elseif (! is_writeable ('../cache')) {
			$data['error'] = true;
			$data['body'] = '<p class="notice">Can\'t write to the Sitellite cache directory</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>In your document root, execute the command `chmod -R 0777 cache`</li></ul>';
		} elseif (! is_writeable ('../pix')) {
			$data['error'] = true;
			$data['body'] = '<p class="notice">Can\'t write to the Sitellite pix directory</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>In your document root, execute the command `chmod -R 0777 pix`</li></ul>';
		} elseif (! is_writeable ('../inc/data')) {
			$data['error'] = true;
			$data['body'] = '<p class="notice">Can\'t write to the Sitellite inc/data directory</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>In your document root, execute the command `chmod -R 0777 inc/data`</li></ul>';
		} elseif (! is_writeable ('../inc/app/cms/data')) {
			$data['error'] = true;
			$data['body'] = '<p class="notice">Can\'t write to the Sitellite inc/app/cms/data directory</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>In your document root, execute the command `chmod -R 0777 inc/app/cms/data`</li></ul>';
		} elseif (! is_writeable ('.')) {
			$data['error'] = true;
			$data['body'] = '<p class="notice">Can\'t write to the Sitellite install directory</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>In your document root, execute the command `chmod 0777 install`</li></ul>';
		} elseif (! extension_loaded ('mysql')) {
			// mysql check
			$data['error'] = true;
			$data['body'] = '<p class="notice">MySQL extension not found.</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>Recompile PHP with the flag --with-mysql</li></ul>';
		} elseif (! extension_loaded ('xml')) {
			// xml check
			$data['error'] = true;
			$data['body'] = '<p class="notice">XML extension not found.</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>Recompile PHP with the flag --with-xml</li></ul>';
		} elseif (! extension_loaded ('pcre')) {
			// pcre check
			$data['error'] = true;
			$data['body'] = '<p class="notice">PCRE extension not found.</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>Recompile PHP with the flag --with-pcre</li></ul>';
		} elseif (! defined ('CRYPT_STD_DES')) {
			// standard crypt() check
			$data['error'] = true;
			$data['body'] = '<p class="notice">Standard DES-based encryption not found.</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>Ensure that the standard DES-based encryption libraries are available on your server and recompile PHP</li></ul>';
		} elseif (ini_get ('allow_url_fopen') == '1' && @join ('', @file ($url . '/install/inc/apache/forcetype/index')) != 'pass') {
			// apache forcetype check
			$data['error'] = true;
			$data['body'] = '<p class="notice">DirectoryIndex test failed.</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>Add the following setting to your httpd.conf configuration file: <a href="http://httpd.apache.org/docs/mod/core.html#allowoverride" target="_blank">AllowOverride All</a></li></ul>';
		} elseif (ini_get ('allow_url_fopen') == '1' && @join ('', @file ($url . '/install/inc/apache/directoryindex/')) != 'pass') {
			// apache directoryindex check
			$data['error'] = true;
			$data['body'] = '<p class="notice">DirectoryIndex test failed.</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>Add the following setting to your httpd.conf configuration file: <a href="http://httpd.apache.org/docs/mod/core.html#allowoverride" target="_blank">AllowOverride All</a></li></ul>';
		} elseif (php_sapi_name () == 'apache2handler' && @join ('', @file ($url . '/install/inc/apache/acceptpathinfo/index.php/test')) != 'pass') {
			// apache 2 acceptpathinfo check
			$data['error'] = true;
			$data['body'] = '<p class="notice">AcceptPathInfo test failed.</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>Add the following setting to your httpd.conf configuration file: <a href="http://httpd.apache.org/docs-2.0/mod/core.html#acceptpathinfo" target="_blank">AcceptPathInfo On</a></li></ul>';
		} elseif (! (
			(
				$ua->property ('browser') == 'ie' &&
				$ua->property ('platform') == 'win' &&
				$ua->property ('version') >= '6.0'
			)
				||
			(
				$ua->property ('browser') == 'mz' &&
				$ua->property ('version') >= '1.5'
			)
				||
			(
				$ua->property ('browser') == 'ns' &&
				$ua->property ('version') >= '7.1'
			)
				||
			(
				$ua->property ('browser') == 'fb' &&
				$ua->property ('version') >= '0.9'
			)
		)) {
			// browser check
			$data['error'] = true;
			$data['body'] = '<p class="notice">Your browser does not meet the minimum requirements.</p>'
				. '<h2>Solution:</h2>'
				. '<ul><li>Use an alternate web browser, such as:'
					. '<ul>'
						. '<li><a href="http://www.mozilla.org/" target="_blank">Firefox 0.9</a> or newer</li>'
						. '<li><a href="http://www.mozilla.org/" target="_blank">Mozilla 1.6</a> or newer</li>'
						. '<li><a href="http://channels.netscape.com/ns/browsers/default.jsp" target="_blank">Netscape 7.1</a> or newer</li>'
						. '<li><a href="http://www.microsoft.com/" target="_blank">Internet Explorer 6</a> or newer</li>'
					. '</ul>'
				. '</li></ul>';
		} elseif (ini_get ('allow_url_fopen') != '1') {
			$data['error'] = true;
			$data['body'] = '<p class="notice">Failed to run the following test due to the <tt>allow_url_fopen</tt> setting being disabled in php.ini:</p>'
				. '<ul><li>AllowOverride All</li></ul>'
				. '<h2>Solutions:</h2>'
				. '<ul><li>Ensure that your httpd.conf configuration file has the <tt>AllowOverride</tt> setting set to <tt>All</tt> then click "Continue installation anyway"</li>'
				. '<li>Enable the <tt>allow_url_fopen</tt> setting in your php.ini configuration file and re-run these tests.  Note that this setting may be disabled for security reasons</li></ul>';
		} else {
			$data['body'] = '<p>The installer has verified that your server and browser meet the <a href="http://www.sitellite.org/index/requirements" target="_blank">minimum installation requirements</a>.  Please click "Next" to continue.</p>';
		}
		if ($data['error']) {
			$data['body'] .= '<p><a href="?step=3">Continue installation anyway</a></p>';
		}
		$data['step'] = 2;
		$data['next_step'] = 3;
		$data['title'] = 'Requirements';
		$data['next'] = 'Settings';
		break;

	case 3:
		if (! isset ($cgi->dbhost)) {
			$cgi->dbhost = 'localhost';
		}
		if (! isset ($cgi->dbport)) {
			$cgi->dbport = 3306;
		}

		$data['step'] = 3;
		$data['next_step'] = 4;
		$data['title'] = 'Settings';
		$data['next'] = 'Installation';
		$data['onclick'] = 'return validate (this.form)';
		$data['body'] = template_simple ('settings.spt', $data);
		
		if (! empty ($cgi->error)) {
			$data['body'] .= '<script language="javascript" type="text/javascript">
				alert (\'' . addslashes ($cgi->error) . '\');
			</script>';
		}

		break;

	case 4:
		$conn = @mysql_connect ($cgi->dbhost . ':' . $cgi->dbport, $cgi->dbuser, $cgi->dbpass);
		if (! $conn) {
			header (
				sprintf (
					'Location: ?step=3&dbhost=%s&dbport=%s&database=%s&dbuser=%s&dbpass=%s&drop=%s&error=%s',
					$cgi->dbhost,
					$cgi->dbport,
					$cgi->database,
					$cgi->dbuser,
					$cgi->dbpass,
					$cgi->drop,
					'Failed to connect to MySQL: ' . mysql_error ()
				)
			);
			exit;
		}
		if (! @mysql_select_db ($cgi->database, $conn)) {
			header (
				sprintf (
					'Location: ?step=3&dbhost=%s&dbport=%s&database=%s&dbuser=%s&dbpass=%s&drop=%s&error=%s',
					$cgi->dbhost,
					$cgi->dbport,
					$cgi->database,
					$cgi->dbuser,
					$cgi->dbpass,
					$cgi->drop,
					'Can\'t use database "' . $cgi->database . '": ' . mysql_error ()
				)
			);
			exit;
		}
		$data['step'] = 4;
		$data['next_step'] = 5;
		$data['title'] = 'Installation';
		$data['next'] = 'Password';
		$data['body'] = template_simple ('installation.spt', $data);
		break;

	case 5:
		$conn = @mysql_connect ($cgi->dbhost . ':' . $cgi->dbport, $cgi->dbuser, $cgi->dbpass);
		if (! $conn) {
			header (
				sprintf (
					'Location: ?step=3&dbhost=%s&dbport=%s&database=%s&dbuser=%s&dbpass=%s&drop=%s&error=%s',
					$cgi->dbhost,
					$cgi->dbport,
					$cgi->database,
					$cgi->dbuser,
					$cgi->dbpass,
					$cgi->drop,
					'Failed to connect to MySQL: ' . mysql_error ()
				)
			);
			exit;
		}
		if (! @mysql_select_db ($cgi->database, $conn)) {
			header (
				sprintf (
					'Location: ?step=3&dbhost=%s&dbport=%s&database=%s&dbuser=%s&dbpass=%s&drop=%s&error=%s',
					$cgi->dbhost,
					$cgi->dbport,
					$cgi->database,
					$cgi->dbuser,
					$cgi->dbpass,
					$cgi->drop,
					'Can\'t use database "' . $cgi->database . '": ' . mysql_error ()
				)
			);
			exit;
		}

		$query = mysql_query ('select version()', $conn);
		$version = mysql_result ($query, 0);
		mysql_free_result ($query);

		if (version_compare ($version, '5.0.0', 'ge')) {
			$sql = @join ('', @file ('install-mysql.sql'));
		} else {
			$sql = @join ('', @file ('install-old.sql'));
		}
		$sql = sql_split ($sql);

		// execute each sql query
		foreach ($sql as $query) {
			if ($cgi->drop == 'yes' && preg_match ('/^create table ([^ ]+) /is', $query, $regs)) {
				@mysql_query ('drop table ' . $regs[1]);
			}
			if (! @mysql_query ($query, $conn)) {
				$data['error'] = true;
				$data['body'] = '<p class="notice">SQL Error: ' . mysql_error () . '</p>';
				break;
			}
		}

		if (! $data['error']) {
			// save info to config.ini.php
			$conf = @join ('', @file ('../inc/conf/config.ini.php'));
			if (($cgi->dbhost != 'localhost') || ($cgi->dbport != 3306)) {
				$conf = str_replace ("hostname\t\t= localhost", "hostname\t\t= \"" . $cgi->dbhost . ':' . $cgi->dbport . '"', $conf);
			}
			$conf = str_replace ("database\t\t= DBNAME", "database\t\t= \"" . $cgi->database . '"', $conf);
			$conf = str_replace ("username\t\t= USER", "username\t\t= \"" . $cgi->dbuser . '"', $conf);
			$conf = str_replace ("password\t\t= PASS", "password\t\t= \"" . $cgi->dbpass . '"', $conf);
			$conf = str_replace ("domain\t\t\t= DOMAIN", "domain\t\t\t= \"" . $_SERVER['HTTP_HOST'] . '"', $conf);

			loader_import ('saf.File');

			if (! file_overwrite ('../inc/conf/config.ini.php', $conf)) {
				$data['error'] = true;
				$data['body'] = '<p class="notice">Error: Unable to save configuration settings</p>';
			}
		}

		$data['step'] = 5;
		$data['next_step'] = 6;
		$data['title'] = 'Password';
		$data['next'] = 'Finish Up';
		if (empty ($data['body'])) {
			$data['onclick'] = 'return validate (this.form)';
			$data['body'] = template_simple ('password.spt', $data);;
		}
		break;

	case 6:
		$conn = @mysql_connect ($cgi->dbhost . ':' . $cgi->dbport, $cgi->dbuser, $cgi->dbpass);
		if (! $conn) {
			header (
				sprintf (
					'Location: ?step=3&dbhost=%s&dbport=%s&database=%s&dbuser=%s&dbpass=%s&drop=%s&error=%s',
					$cgi->dbhost,
					$cgi->dbport,
					$cgi->database,
					$cgi->dbuser,
					$cgi->dbpass,
					$cgi->drop,
					'Failed to connect to MySQL: ' . mysql_error ()
				)
			);
			exit;
		}
		if (! @mysql_select_db ($cgi->database, $conn)) {
			header (
				sprintf (
					'Location: ?step=3&dbhost=%s&dbport=%s&database=%s&dbuser=%s&dbpass=%s&drop=%s&error=%s',
					$cgi->dbhost,
					$cgi->dbport,
					$cgi->database,
					$cgi->dbuser,
					$cgi->dbpass,
					$cgi->drop,
					'Can\'t use database "' . $cgi->database . '": ' . mysql_error ()
				)
			);
			exit;
		}

		// set password
		if (! @mysql_query ('update sitellite_user set password = "' . better_crypt ($cgi->pass) . '" where username = "admin"', $conn)) {
			$data['error'] = true;
			$data['body'] = '<p class="notice">Error setting password: ' . mysql_error () . '</p>';
		}

		$data['step'] = 6;
		$data['next_step'] = 7;
		$data['title'] = 'Finish Up';
		$data['next'] = 'Finish';
		if (empty ($data['body'])) {
			$data['body'] = template_simple ('finish.spt', $data);
		}
		break;

	default:
		// mark the installation completed
		@umask (0000);
		@touch ('installed');

		$data['step'] = -1;
		$data['new_site'] = '../sitellite';
		$data['title'] = 'Finished';
		$data['body'] = template_simple ('finished.spt', $data);
		break;
}

echo template_simple ('layout.spt', $data);

?>