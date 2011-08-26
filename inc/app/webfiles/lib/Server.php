<?php

// For those pear and ext packages that use the include_path:
if (strtoupper (substr (PHP_OS, 0, 3)) === 'WIN') {
	$join = ';';
} else {
	$join = ':';
}
ini_set ('include_path', 'inc/app/webfiles/lib/PEAR' . $join . ini_get ('include_path'));

loader_import ('cms.Versioning.Rex');
loader_import ('webfiles.PEAR.HTTP.WebDAV.Server');

/**
 * Webfiles access using WebDAV
 *
 * @access  public
 * @author  Hartmut Holzgraefe <hartmut@php.net>
 * @version @package-version@
 */
class HTTP_WebDAV_Server_Webfiles extends HTTP_WebDAV_Server {
	/**
	 * Rex object for the sitellite_filesystem collection.
	 */
	var $rex;

	var $url;

	var $base;

	var $debug;

	/**
	 * Serve a webdav request
	 *
	 * @access public
	 * @param  string
	 */
	function ServeRequest () {
		// special treatment for litmus compliance test
		// reply on its identifier header
		// not needed for the test itself but eases debugging
		foreach (apache_request_headers () as $key => $value) {
			if (stristr ($key, "litmus")) {
				error_log ("Litmus test $value");
				header ("X-Litmus-reply: " . $value);
			}
		}

		$this->http_auth_realm = 'Web Files';

		$this->dav_powered_by = 'Sitellite Content Server ' . SITELLITE_VERSION;

		$this->rex = new Rex ('sitellite_filesystem');

		$this->url = site_prefix () . '/webfiles-app';

		$this->_SERVER['SCRIPT_NAME'] = '/webfiles-app';

		$this->base = 'inc/data';

		$this->debug = appconf ('debug');

		// let the base class do all the work
		parent::ServeRequest ();
	}

	/**
	 * No authentication is needed here
	 *
	 * @access private
	 * @param  string  HTTP Authentication type (Basic, Digest, ...)
	 * @param  string  Username
	 * @param  string  Password
	 * @return bool	true on successful authentication
	 */
	function check_auth ($type, $user, $pass) {
		// for testing without auth, uncomment these...
		// note: requires a real user in sitellite with these creds
		//$user = 'dav';
		//$pass = 'webdav';

		if (! $user) {
			return false;
		}

		db_execute (
			'insert into sitellite_log
				(ts, type, user, ip, request, message)
			values
				(now(), ?, ?, ?, ?, ?)',
			'dav_auth',
			'dav',
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['REQUEST_URI'],
			$type . ' - ' . $user . ' - ' . $pass
		);

		global $session;
		$session->username = $user;
		$session->password = $pass;
		$session->start ();
		if (! $session->valid) {
			return false;
		}
		if (! $session->allowed ('sitellite_filesystem', 'rw', 'resource')) {
			return false;
		}
		return true;
	}


	/**
	 * PROPFIND method handler
	 *
	 * @param  array  general parameter passing array
	 * @param  array  return array for file properties
	 * @return bool   true on success
	 */
	function PROPFIND (&$options, &$files) {
		// get absolute fs path to requested resource
		$options['path'] = $this->_path ();
		$this->_debug (__LINE__, 0, 'PROPFIND: ' . $options['path']);
		$fspath = $this->base . $options["path"];

		// sanity check
		if (! file_exists (strtolower ($fspath))) {
			return false;
		}

		// prepare property array
		$files["files"] = array ();

		// store information for the requested path itself
		$files["files"][] = $this->fileinfo ($options["path"]);

		// information for contained resources requested?
		if (! empty ($options["depth"])) { // TODO check for is_dir() first?

			// make sure path ends with '/'
			$options["path"] = $this->_slashify ($options["path"]);

			// try to open directory
			$handle = @opendir ($fspath);

			if ($handle) {
				// ok, now get all its contents
				while ($filename = readdir ($handle)) {
					if (strpos ($filename, '.') === 0 || $filename == 'CVS' || $filename == 'index') {
						continue;
					}

					$fullpath = $this->_slashify ($fspath) . $filename;
					$info = array ();

					if (! @is_dir ($fullpath) && strpos ($fspath, '/.') === false) {
						$info = $this->rex->getCurrent (substr ($options['path'], 1) . $filename);
						unset ($info->body);
						unset ($info->name);
						unset ($info->path);
						unset ($info->extension);
						unset ($info->display_title);
						unset ($info->filesize);
						unset ($info->last_modified);
						unset ($info->date_created);
						if (! session_allowed ($info, 'rw')) {
							continue;
						}
					}

					$files["files"][] = $this->fileinfo ($options["path"] . $filename, $info);
				}
				// TODO recursion needed if "Depth: infinite"
			}
		}

		// ok, all done
		return true;
	}

	/**
	 * Get properties for a single file/resource
	 *
	 * @param  string  resource path
	 * @return array   resource properties
	 */
	function fileinfo ($path, $extras = array ()) {
		// map URI path to filesystem path
		//global $cgi;
		//$path = str_replace ('//', '/', '/' . $cgi->path);
		$fspath = $this->base . $path;

		// create result array
		$info = array();
		// TODO remove slash append code when base clase is able to do it itself
		$info["path"]  = is_dir($fspath) ? $this->_slashify($path) : $path;
		$info["props"] = array();

		// no special beautified displayname here ...
		//$info["props"][] = $this->mkprop("displayname", strtoupper($path));
		$info['props'][] = $this->mkprop ('displayname', basename ($path));

		// creation and modification time
		$info["props"][] = $this->mkprop("creationdate",	filectime($fspath));
		$info["props"][] = $this->mkprop("getlastmodified", filemtime($fspath));

		// type and size (caller already made sure that path exists)
		if (is_dir($fspath)) {
			// directory (WebDAV collection)
			$info["props"][] = $this->mkprop("resourcetype", "collection");
			$info["props"][] = $this->mkprop("getcontenttype", "httpd/unix-directory");			
		} else {
			// plain file (WebDAV resource)
			$info["props"][] = $this->mkprop("resourcetype", "");
			if (is_readable($fspath)) {
				$info["props"][] = $this->mkprop("getcontenttype", $this->_mimetype($fspath));
			} else {
				$info["props"][] = $this->mkprop("getcontenttype", "application/x-non-readable");
			}			
			$info["props"][] = $this->mkprop("getcontentlength", filesize($fspath));
		}

		// add any Sitellite-specific properties as well
		foreach ((array) $extras as $k => $v) {
			$info['props'][] = $this->mkprop ('WebFiles', str_replace ('sitellite_', '', $k), $v);
		}

		// get additional properties from database
		/*$query = "SELECT ns, name, value
						FROM {$this->db_prefix}properties
					   WHERE path = '$path'";
		$res = mysql_query($query);
		while ($row = mysql_fetch_assoc($res)) {
			$info["props"][] = $this->mkprop($row["ns"], $row["name"], $row["value"]);
		}
		mysql_free_result($res);*/

		return $info;
	}

	/**
	 * try to detect the mime type of a file
	 *
	 * @param  string  file path
	 * @return string  guessed mime type
	 */
	function _mimetype ($fspath) {
		if (@is_dir ($fspath)) {
			return 'httpd/unix-directory';
		}
		return mime ($fspath, 'application/octet-stream');
	}

	/**
	 * get the path based on the REQUEST_URI.
	 * path is everything after /webdav-app.
	 *
	 * @return string path
	 */
	function _path ($p = false) {
		if (! $p) {
			$p = $_SERVER['REQUEST_URI'];
		}
		$exploded = explode ('/webfiles-app/', $p);
		if (count ($exploded) < 2) {
			return '/';
		}
		$path = array_pop ($exploded);
		if (empty ($path)) {
			return '/';
		}
		return '/' . $this->_fix_name ($path);
	}

	/**
	 * Fix file and folder names for Sitellite.
	 *
	 * @param string name
	 * @return string name
	 */
	function _fix_name ($name) {
		$name = urldecode ($name);
		//$name = strtolower ($name);
		//$name = preg_replace ('/[^a-z0-9\.\/_-]+/', '_', $name);
		return $name;
	}

	function _debug ($line, $errno, $msg) {
		if ($this->debug == 'enabled') {
			db_execute ('insert into webfiles_log values (null, ?, ?, ?, now())', $line, $errno, $msg);
		}
	}

	/**
	 * GET method handler
	 *
	 * @param  array  parameter passing array
	 * @return bool   true on success
	 */
	function GET (&$options) {
		// get absolute fs path to requested resource
		//global $cgi;
		//$options['path'] = str_replace ('//', '/', '/' . $cgi->path);
		$options['path'] = $this->_path ();
		$this->_debug (__LINE__, 0, 'GET: ' . $options['path']);

		$fspath = strtolower ($this->base . $options["path"]);

		// sanity check
		if (! file_exists ($fspath)) {
			return false;
		}

		// is this a collection?
		if (is_dir ($fspath)) {
			return $this->GetDir ($fspath, $options);
		}

		$info = $this->rex->getCurrent (substr ($options['path'], 1));
		unset ($info->body);
		if (! session_allowed ($info, 'rw')) {
			$this->_debug (__LINE__, 403, 'Forbidden: ' . $info->name);
			return '403 Forbidden';
		}

		// detect resource type
		$options['mimetype'] = $this->_mimetype ($fspath);

		// detect modification time
		// see rfc2518, section 13.7
		// some clients seem to treat this as a reverse rule
		// requiering a Last-Modified header if the getlastmodified header was set
		$options['mtime'] = filemtime ($fspath);

		// detect resource size
		$options['size'] = filesize ($fspath);

		// no need to check result here, it is handled by the base class
		$options['stream'] = fopen ($fspath, "r");

		return true;
	}

	/**
	 * GET method handler for directories
	 *
	 * This is a very simple mod_index lookalike.
	 * See RFC 2518, Section 8.4 on GET/HEAD for collections
	 *
	 * @param  string  directory path
	 * @return void	function has to handle HTTP response itself
	 */
	function GetDir ($fspath, &$options) {
		$options['path'] = $this->_path ();
		$path = $this->_slashify ($options["path"]);
		/*if ($path != $options["path"]) {
			header("Location: " . $path);
			exit;
		}*/

		// fixed width directory column format
		$format = "%15s  %-19s  %-s\n";

		$fspath = strtolower ($fspath);

		$handle = @opendir ($fspath);
		if (! $handle) {
			return false;
		}

		echo "<html><head><title>Index of " . htmlspecialchars ($options['path']) . "</title></head>\n";

		echo "<h1>Index of " . htmlspecialchars ($options['path']) . "</h1>\n";

		echo "<pre>";
		printf ($format, "Size", "Last modified", "Filename");
		echo "<hr>";

		$url = $this->url;

		while ($filename = readdir ($handle)) {
			if (strpos ($filename, '.') === 0 || $filename == 'CVS') {
				continue;
			}

			$fullpath = $fspath . "/" . $filename;
			$name = htmlspecialchars ($filename);

			if (! @is_dir ($fullpath)) {
				$info = $this->rex->getCurrent (substr ($path, 1) . $filename);
				unset ($info->body);
				if (! session_allowed ($info, 'rw')) {
					continue;
				}
			}

			printf (
				$format,
				number_format (filesize ($fullpath)),
				strftime ("%Y-%m-%d %H:%M:%S", filemtime ($fullpath)),
				"<a href='$url$path$name'>$name</a>"
			);
		}

		echo "</pre>";

		closedir ($handle);

		echo "</html>\n";

		exit;
	}

	/**
	 * PUT method handler
	 *
	 * @param  array  parameter passing array
	 * @return bool   true on success
	 */
	function PUT (&$options) {
		// options[path] is mixed-case
		$options['path'] = $this->_path ();
		$this->_debug (__LINE__, 0, 'PUT: ' . $options['path']);

		// orig is mixed-case
		$orig = basename ($options['path']);

		// fspath is mixed-case
		$fspath = $this->base . $options["path"];

		// checkLock() will adjust to lowercase
		if ($this->checkLock ($options['path'], true)) {
			$this->_debug (__LINE__, 409, 'Locked: ' . $options['path']);
			return '409 Conflict';
		}

		// make sure the dir exists
		// we convert to lowercase here
		if (! @is_dir (dirname (strtolower ($fspath)))) {
			$this->_debug (__LINE__, 409, 'Not a dir: ' . $fspath);
			return "409 Conflict";
		}

		// check if the file exists
		// we convert to lowercase here
		$options["new"] = ! file_exists (strtolower ($fspath));

		// ranges not supported
		if (! empty ($options['ranges'])) {
			$this->_debug (__LINE__, 501, 'Ranges not implemented');
			return '501 Not implemented';
		}

		// store dot-files directly (used by mac os x for example)
		if (strpos ($options['path'], '/.') !== false && strpos ($options['path'], '.htaccess') === false) {
			$this->_debug (__LINE__, 0, 'Storing dot-file: ' . $options['path']);
			//return '403 Forbidden';

			$body = '';
			while (! feof ($options['stream'])) {
				$body .= fread ($options['stream'], 4096);
			}
			fclose ($options['stream']);

			// conver to lowercase for storing
			if (file_exists (strtolower ($fspath))) {
				file_put_contents (strtolower ($fspath), $body);
				return '200 OK';
			}

			// convert to lowercase for storing
			file_put_contents (strtolower ($fspath), $body);

			umask (0000);
			chmod (strtolower ($fspath), 0777);

			return '201 Created';
		}

		if ($options['new']) {
			if (session_is_resource ('add') && ! session_allowed ('add', 'rw', 'resource')) {
				$this->_debug (__LINE__, 403, 'Permissions failed: add');
				return '403 Forbidden';
			}

			$body = '';
			while (! feof ($options['stream'])) {
				$body .= fread ($options['stream'], 4096);
			}
			fclose ($options['stream']);

			// convert to lowercase for storing
			$vals = array (
				'name' => ltrim (strtolower ($options['path']), '/'),
				'display_title' => $orig,
				'keywords' => '',
				'description' => '',
				'body' => $body,
				'sitellite_status' => 'draft',
				'sitellite_access' => 'private',
				'sitellite_owner' => session_username (),
				'sitellite_team' => session_team (),
			);
			$res = $this->rex->create ($vals, 'Uploaded via WebDAV.');
			if (! $res) {
				$this->_debug (__LINE__, 500, 'Create failed: ' . $vals['name']);
				return '500 Internal server error';
			}
			return '201 Created';
		} else {
			$vals = (array) $this->rex->getCurrent (ltrim (strtolower ($options['path']), '/'));

			if (! session_allowed ($vals, 'rw')) {
				$this->_debug (__LINE__, 403, 'Permissions failed: ' . $vals['name']);
				return '403 Forbidden';
			}

			$vals['body'] = '';
			while (! feof ($options['stream'])) {
				$vals['body'] .= fread ($options['stream'], 4096);
			}
			fclose ($options['stream']);

			unset ($vals['name']);
			unset ($vals['path']);
			unset ($vals['extension']);
			unset ($vals['filesize']);
			unset ($vals['last_modified']);
			unset ($vals['date_created']);

			$method = $this->rex->determineAction (ltrim (strtolower ($options['path']), '/'), $vals['sitellite_status']);
			if (! $method) {
				$this->_debug (__LINE__, 500, 'No method: ' . $options['path']);
				return '500 Internal server error';
			}
			$res = $this->rex->{$method} (ltrim (strtolower ($options['path']), '/'), $vals, 'Updated via WebDAV.');
			if (! $res) {
				$this->_debug (__LINE__, 500, 'Rex error: ' . $this->rex->error . ' (' . $options['path'] . ')');
				return '500 Internal server error';
			}
			return '200 OK';
		}

		//$fp = fopen ($fspath, "w");

		//return $fp;
	}

	/**
	 * MKCOL method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function MKCOL ($options) {
		$options['path'] = $this->_path ();
		$path = $this->base . $options["path"];
		$parent = strtolower (dirname ($path));
		$name = strtolower (basename ($path));

		// fix folder names for sitellite
		$name = $this->_fix_name ($name);

		// strip dot-files
		if (strpos ($name, '.') === 0) {
			$this->_debug (__LINE__, 403, 'Can\'t upload dot-files: ' . $name);
			return '403 Forbidden';
		}

		if (! file_exists ($parent)) {
			$this->_debug (__LINE__, 409, 'Parent doesn\'t exist: ' . $parent);
			return "409 Conflict";
		}

		if (! is_dir ($parent)) {
			$this->_debug (__LINE__, 403, 'Parent isn\'t a directory: ' . $parent);
			return "403 Forbidden";
		}

		if (file_exists ($parent . "/" . $name)) {
			$this->_debug (__LINE__, 405, 'Folder already exists: ' . $parent . '/' . $name);
			return "405 Method not allowed";
		}

		if (! empty ($this->_SERVER["CONTENT_LENGTH"])) { // no body parsing yet
			$this->_debug (__LINE__, 415, 'Unsupported media type?');
			return "415 Unsupported media type";
		}

		umask (0000);
		$stat = mkdir ($parent . "/" . $name, 0777);
		if (! $stat) {
			$this->_debug (__LINE__, 403, 'Mkdir failed: ' . $parent . '/' . $name);
			return "403 Forbidden";				
		}

		return ("201 Created");
	}

	/**
	 * DELETE method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function DELETE ($options) {
		if (isset ($options['dest'])) {
			$options['path'] = $options['dest'];
		} else {
			$options['path'] = $this->_path ();
		}
		$path = $this->base . strtolower (rtrim ($options["path"], '/'));

		$debug = array ();
		foreach ($options as $k => $v) {
			$debug[] = $k . '=' . $v;
		}
		$this->_debug (__LINE__, 0, 'DELETE: ' . join (', ', $debug));

		if ($this->checkLock ($options['path'], true)) {
			$this->_debug (__LINE__, 423, 'Locked : ' . $options['path']);
			return '423 Locked';
		}

		if (! file_exists ($path)) {
			$this->_debug (__LINE__, 404, 'File doesn\'t exist: ' . $path);
			return "404 Not found";
		}

		if (session_is_resource ('delete') && ! session_allowed ('delete', 'rw', 'resource')) {
			$this->_debug (__LINE__, 403, 'Permissions failed: delete');
			return '403 Forbidden';
		}

		if (is_dir ($path)) {
			return $this->_rmdir_recursive (trim ($path, '/'));
		} elseif (strpos ($path, '/.') !== false) {
			// dot-file
			$res = unlink ($path);
			if (! $res) {
				$this->_debug (__LINE__, 403, 'Unlinking dot-file failed: ' . $path);
				return '403 Forbidden';
			}
		} else {
			$info = $this->rex->getCurrent (ltrim ($options['path'], '/'));
			if (! session_allowed ($info, 'rw')) {
				$this->_debug (__LINE__, 403, 'Permissions failed: ' . $info->name);
				return '403 Forbidden';
			}
			if (! $this->rex->delete (ltrim ($options['path'], '/'), 'Deleted via WebDAV.')) {
				$this->_debug (__LINE__, 500, 'Delete failed: ' . $this->rex->error . ' (' . $options['path'] . ')');
				return '500 Internal server error';
			}
		}

		return "204 No Content";
	}

	function _rmdir_recursive ($path) {
		$d = dir ($path);
		while ($file = $d->read ()) {
			if ($file != '.' && $file != '..') {
				if (@is_writeable ($path . '/' . $file)) {
					if (@is_dir ($path . '/' . $file) && ! @is_link ($path . '/' . $file)) {
						$err = $this->_rmdir_recursive ($path . '/' . $file);
						if (strpos ($err, '200 ') === false) {
							return $err;
						}
					} else {
						// delete with rex
						$info = $this->rex->getCurrent ($path . '/' . $file);
						if (! session_allowed ($info, 'rw')) {
							$this->_debug (__LINE__, 403, 'Permissions failed: ' . $path . '/' . $file);
							return '403 Forbidden';
						}
						if (! $this->rex->delete (str_replace ('inc/data/', '', $path . '/' . $file), 'Deleted via WebDAV.')) {
							$this->_debug (__LINE__, 500, 'Delete failed: ' . $this->rex->error . ' (' . $path . '/' . $file . ')');
							return '500 Internal server error';
						}
					}
				} else {
					$this->_debug (__LINE__, 403, 'Not writeable: ' . $path . '/' . $file);
					return '403 Forbidden';
				}
			}
		}
		$d->close ();
		$res = rmdir ($path);
		if (! $res) {
			$this->_debug (__LINE__, 500, 'Delete failed: ' . $path);
			return '500 Internal server error';
		}
		return '200 No Content';
	}

	/**
	 * MOVE method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function MOVE ($options) {
		return $this->COPY ($options, true);
	}

	/**
	 * COPY method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function COPY ($options, $del = false) {
		// TODO Property updates still broken (Litmus should detect this?)
		$options['path'] = rtrim ($this->_path (), '/');
		$options['dest'] = rtrim ($this->_fix_name ($options['dest']), '/');
		// $options['path'] is '/path/file1.ext'
		// $options['dest'] is '/path/file2.ext'
		// these do not include /webfiles-app nor /inc/data

		// strip dot-files
		if (strpos ($options['dest'], '/.') !== false) {
			$this->_debug (__LINE__, 403, 'Can\'t upload dot-files: ' . $options['dest']);
			return '403 Forbidden';
		}

		if ($del && $this->checkLock ($options['path'], true)) {
			$this->_debug (__LINE__, 423, 'Locked: ' . $options['path']);
			return '423 Locked';
		}

		if (! empty ($this->_SERVER["CONTENT_LENGTH"])) { // no body parsing yet
			$this->_debug (__LINE__, 415, 'Unsupported media type?');
			return "415 Unsupported media type";
		}

		// no copying to different WebDAV Servers yet
		// dest_url is set if the url is not within the same webdav repository
		if (isset ($options["dest_url"])) {
			$this->_debug (__LINE__, 502, 'Can\'t copy from one repos to another: ' . $options['dest_url']);
			return "502 bad gateway";
		}

		// $source is inc/data/path/file1.ext
		$source = strtolower ($this->base . $options["path"]);
		if (! file_exists ($source)) {
			$this->_debug (__LINE__, 404, 'Not found: ' . $source);
			return "404 Not found";
		}

		// $dest is inc/data/path/file2.ext
		$dest		  = strtolower ($this->base . $options["dest"]);
		$new		  = ! file_exists ($dest);
		$existing_col = false;

		// this part is still fuzzy...
		if (! $new) {
			if ($del && is_dir ($dest)) {
				if (! $options["overwrite"]) {
					$this->_debug (__LINE__, 412, 'Conditions failed since dir already exists: ' . $dest . ' (new=false, del=true, is_dir=true, overwrite=false)');
					return "412 precondition failed";
				}
				//$dest .= basename ($source);
				//if (file_exists ($dest)) {
				//	$options["dest"] .= basename ($source);
				//} else {
				//	$new		  = true;
				//	$existing_col = true;
				//}
			}
		}

		// delete destination first if we're overwriting
		if (! $new) {
			if ($options["overwrite"]) {
				if (@file_exists ($dest)) {
					$stat = $this->DELETE (array ("dest" => $options["dest"]));
					if (($stat{0} != "2") && (substr ($stat, 0, 3) != "404")) {
						$this->_debug (__LINE__, substr ($stat, 0, 3), 'Failed on delete: ' . $options['dest']);
						return $stat;
					}
				}
			} elseif (@file_exists ($dest) && ! $options['overwrite']) {
				$this->_debug (__LINE__, 412, 'Conditions failed since file already exists: ' . $dest . ' (new=false, file_exists=true, overwrite=false)');
				return "412 precondition failed";
			}
		}

		//if (is_dir ($source) && ($options["depth"] != "infinity")) {
			// RFC 2518 Section 9.2, last paragraph
			//return "400 Bad request";
		//}

		if ($del) {
			loader_import ('saf.File.Directory');

			if (is_dir ($source)) {
				$files = array_merge (array ($source), Dir::find ('*', $source, 1));
				//$files = array_reverse ($files);

				if (! is_array ($files) || count ($files) == 0) {
					$this->_debug (__LINE__, 500, 'No files from source: ' . $source);
					return '500 Internal server error';
				}

				// todo: handle recursive moves!!!

				foreach ($files as $file) {
					if ($file == $source) {
						if (! mkdir ($dest, 0777)) {
							$this->_debug (__LINE__, 409, 'Mkdir failed: ' . $dest);
							return '409 Conflict';
						}
					} elseif (is_dir ($file)) {
						$destfile = str_replace ($source, $dest, $file);
						$res = Dir::build ($destfile, 0777);
						if (! $res) {
							$this->_debug (__LINE__, 409, 'Mkdir recursive failed: ' . $destfile);
							return '409 Conflict';
						}
					} elseif (! @is_dir ($file)) {
						$info = $this->rex->getCurrent (preg_replace ('|^inc/data/|', '', $file));
						if (! session_allowed ($info, 'rw')) {
							$this->_debug (__LINE__, 403, 'Permissions failed: ' . $this->rex->error . ' (' . $info->name . ')');
							return '403 Forbidden';
						}

						$destfile = str_replace ($source, $dest, $file);

						$method = $this->rex->determineAction (preg_replace ('|^inc/data/|', '', $file));
						$res = $this->rex->{$method} (preg_replace ('|^inc/data/|', '', $file), array ('name' => preg_replace ('|^inc/data/|', '', $destfile)));
						if (! $res) {
							$this->_debug (__LINE__, 500, 'Unknown rex error: ' . $this->rex->error . ' (' . $destfile . ')');
							return '500 Internal server error';
						}
					}
				}

				// erase the source once everything's been moved over successfully
				if (@file_exists ($source)) {
					$this->DELETE (array ('dest' => $options['path']));
				}
			} else {
				$info = $this->rex->getCurrent (trim ($options['path'], '/'));
				if (! session_allowed ($info, 'rw')) {
					$this->_debug (__LINE__, 403, 'Permissions failed: ' . $info->name);
					return '403 Forbidden';
				}

				$method = $this->rex->determineAction (trim ($options['path'], '/'));
				$res = $this->rex->{$method} (trim ($options['path'], '/'), array ('name' => trim ($options['dest'], '/')));
				if (! $res) {
					$this->_debug (__LINE__, 500, 'Unknown rex error: ' . $this->rex->error . ' (' . $options['dest'] . ')');
					return '500 Internal server error';
				}
			}
		} else {
			loader_import ('saf.File.Directory');

			if (is_dir ($source)) {
				//$files = System::find ($source);
				$files = array_merge (array ($source), Dir::find ('*', $source, 1));
				$files = array_reverse ($files);
			} else {
				$files = array ($source);
			}

			$single = (count ($files) == 1) ? true : false;

			if (! is_array ($files) || count ($files) == 0) {
				$this->_debug (__LINE__, 500, 'No files from source: ' . $source);
				return "500 Internal server error";
			}

			foreach ($files as $file) {
				if (is_dir ($file)) {
					$file = $this->_slashify ($file);
				}

				$destfile = str_replace ($source, $dest, $file);

				if (is_dir ($file)) {
					if (! is_dir ($destfile)) {
						$res = Dir::build ($destfile, 0777);
						if (! $res) {
							$this->_debug (__LINE__, 409, 'Mkdir recursive failed: ' . $destfile);
							return '409 Conflict';
						}
					}
				} else {
					if ($single && ! @is_dir (dirname ($destfile))) {
						$this->_debug (__LINE__, 409, 'Not a directory: ' . $destfile);
						return '409 Conflict';
					}

					if (! $options['overwrite'] && @file_exists ($destfile)) {
						$this->_debug (__LINE__, 409, 'File exists, overwrite not set: ' . $destfile);
						return '409 Conflict';
					}

					$info = (array) $this->rex->getCurrent (preg_replace ('|^inc/data/|', '', $file));
					if (! session_allowed ($info, 'r')) {
						$this->_debug (__LINE__, 403, 'Permissions failed: ' . $info['name']);
						return '403 Forbidden';
					}
					$info['name'] = preg_replace ('|^inc/data/|', '', $destfile);
					unset ($info['filesize']);
					unset ($info['last_modified']);
					unset ($info['date_created']);
					$info['sitellite_status'] = 'draft';
					$info['sitellite_access'] = 'private';
					$res = $this->rex->create ($info, 'Copied via WebDAV.');
					if (! $res) {
						$this->_debug (__LINE__, 409, 'Unknown rex error: ' . $this->rex->error . ' (' . $infp['name'] . ')');
						return "409 Conflict";
					}
				}
			}
		}

		return ($new && ! $existing_col) ? "201 Created" : "204 No Content";
	}

	/**
	 * PROPPATCH method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function PROPPATCH (&$options) {
		$options['path'] = $this->_path ();
		$path = $options["path"];

		//info ($options);

		$info = $this->rex->getCurrent (trim ($path, '/'));
		if (! session_allowed ($info, 'rw')) {
			foreach ($options['props'] as $k => $v) {
				$options['props'][$k]['status'] = '403 Forbidden';
			}
			return '';
		}

		unset ($info);
		$update = array ();

		foreach ($options["props"] as $key => $prop) {
			if ($prop["ns"] == "DAV:") {
				$options["props"][$key]['status'] = "403 Forbidden";
			} elseif ($prop['ns'] == 'WebFiles') {
				// known properties by sitellite
				if (isset ($prop['val'])) {
					switch ($prop['name']) {
						case 'status':
						case 'access':
						case 'owner':
						case 'team':
							$update['sitellite_' . $prop['name']] = $prop['val'];
							break;
						case 'keywords':
						case 'description':
							$update[$prop['name']] = $prop['val'];
							break;
					}
				} else {
					// todo: delete property value
				}
			}
		}

		//info ($update);
		// todo: update rex with $update...

		return '';
	}


	/**
	 * LOCK method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function LOCK (&$options) {
		// get absolute fs path to requested resource
		$options['path'] = strtolower ($this->_path ());
		$fspath = $this->base . $options["path"];

		loader_import ('cms.Workflow.Lock');
		lock_init ();

		if ($this->checkLock ($options['path'], $options['locktoken'])) {
			$this->_debug (__LINE__, 409, 'Locked: ' . $options['path']);
			return '423 Locked';
		}

		if (@is_dir ($fspath)) {
			$info = lock_info ('sitellite_filesystem', trim ($options['path'], '/'));
			if ($info) {
				if (lock_update ('sitellite_filesystem', trim ($options['path'], '/'))) {
					$options['owner'] = $info->user;
					$options['scope'] = 'exclusive';
					$options['type'] = 'write';
					$options['timeout'] = time () + appconf ('lock_timeout');
					$options['locktoken'] = $info->token;
					return '200 OK';
				}
			} elseif (local_add ('sitellite_filesystem', trim ($options['path'], '/'), $options['locktoken'])) {
				$info = lock_info ('sitellite_filesystem', trim ($options['path'], '/'));
				$options['owner'] = $info->user;
				$options['scope'] = 'exclusive';
				$options['type'] = 'write';
				$options['timeout'] = time () + appconf ('lock_timeout');
				return '200 OK';
			}
			$this->_debug (__LINE__, 409, 'Locked: ' . $options['path']);
			return '409 Conflict';
		} else {
			$info = lock_info ('sitellite_filesystem', trim ($options['path'], '/'));
			if ($info) {
				if (lock_update ('sitellite_filesystem', trim ($options['path'], '/'))) {
					$options['owner'] = $info->user;
					$options['scope'] = 'exclusive';
					$options['type'] = 'write';
					$options['timeout'] = time () + appconf ('lock_timeout');
					$options['locktoken'] = $info->token;
					return '200 OK';
				}
			} elseif (lock_add ('sitellite_filesystem', trim ($options['path'], '/'), $options['locktoken'])) {
				$info = lock_info ('sitellite_filesystem', trim ($options['path'], '/'));
				$options['owner'] = $info->user;
				$options['scope'] = 'exclusive';
				$options['type'] = 'write';
				$options['timeout'] = time () + appconf ('lock_timeout');
				return '200 OK';
			}
			$this->_debug (__LINE__, 409, 'Locked: ' . $options['path']);
			return '409 Conflict';
		}
	}

	/**
	 * UNLOCK method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function UNLOCK (&$options) {
		$path = trim ($this->_path (), '/');
		$fspath = $this->base . '/' . $path;

		if ($this->checkLock ($path, true)) {
			$this->_debug (__LINE__, 409, 'Locked: ' . $path);
			return '409 Conflict';
		}

		loader_import ('cms.Workflow.Lock');
		lock_init ();

		if (is_dir ($fspath)) {
			$info = lock_info ('sitellite_filesystem', $path);
			if ($options['token'] != $info->token) {
				$this->_debug (__LINE__, 403, 'Token didn\'t match: ' . $path . ' (real: ' . $info->token . ', sent: ' . $options['token'] . ')');
				return '403 Forbidden';
			}
			if (lock_remove ('sitellite_filesystem', $path)) {
				return true;
			}
		} else {
			$info = lock_info ('sitellite_filesystem', $path);
			if ($options['token'] != $info->token) {
				$this->_debug (__LINE__, 403, 'Token didn\'t match: ' . $path . ' (real: ' . $info->token . ', sent: ' . $options['token'] . ')');
				return '403 Forbidden';
			}
			if (lock_remove ('sitellite_filesystem', $path)) {
				return true;
			}
		}
		$this->_debug (__LINE__, 500, 'Lock remove must have failed: ' . $path);
		return '500 Internal server error';
	}

	/**
	 * checkLock() helper
	 *
	 * @param  string resource path to check for locks
	 * @return bool   true on success
	 */
	function checkLock ($path, $token = false) {
		$path = strtolower (trim ($path, '/'));
		/*if (! $sitellite) {
			// called by parent class
			$path = trim ($this->_path (), '/');
		}*/

		loader_import ('cms.Workflow.Lock');
		lock_init ();

		if (@is_dir ($this->base . '/' . $path)) {
			if (lock_exists ('sitellite_filesystem', $path)) {
				$info = lock_info ('sitellite_filesystem', $path);
				if ($token && $token != $info->token) {
					return false;
				}
				return array (
					'type' => 'write',
					'scope' => 'exclusive',
					'depth' => 'infinite',
					'owner' => $info->user,
					'token' => $info->token,
					'created' => strtotime ($info->created),
					'modified' => strtotime ($info->modified),
					'expires' => strtotime ($info->expires),
				);
			}
		} else {
			if (lock_exists ('sitellite_filesystem', $path)) {
				$info = lock_info ('sitellite_filesystem', $path);
				if ($token && $token != $info->token) {
					return false;
				}
				return array (
					'type' => 'write',
					'scope' => 'exclusive',
					'depth' => 0,
					'owner' => $info->user,
					'token' => $info->token,
					'created' => strtotime ($info->created),
					'modified' => strtotime ($info->modified),
					'expires' => strtotime ($info->expires),
				);
			}

			// TODO: check for locks on folders as well
		}
		return false;
	}
}

?>