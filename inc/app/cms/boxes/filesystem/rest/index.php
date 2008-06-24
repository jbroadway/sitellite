<?php

/* for testing
global $session;
$session->username = 'admin';
$session->password = 'admin';
$session->start ();
// */

// authenticate clients (http basic)
if (! isset ($_SERVER['PHP_AUTH_USER']) && ! session_admin ()) {
	header ('HTTP/1.1 401 Unauthorized');
	header ('WWW-Authenticate: Basic realm="Web Files"');
	echo 'Authorization is required to access this resource.';
	exit;
} else {
	global $session;
	$session->username = $_SERVER['PHP_AUTH_USER'];
	$session->password = $_SERVER['PHP_AUTH_PW'];
	$session->start ();
	if (! session_admin ()) {
		header ('HTTP/1.1 401 Unauthorized');
		header ('WWW-Authenticate: Basic realm="Web Files"');
		echo 'Authorization is required to access this resource.';
		exit;
	}
}

loader_import ('saf.File.Directory');
loader_import ('cms.Versioning.Rex');
loader_import ('cms.Workflow.Lock');
loader_import ('cms.Workflow');

function json ($data) {
	if (function_exists ('json_encode')) {
		$out = json_encode ($data);
	} else {
		loader_import ('pear.Services.JSON');
		$json = new Services_JSON ();
		$out = $json->encode ($data);
	}
	return str_replace ('\\/', '/', $out);
}

// parses the request for the switch below
function webfiles_request () {
	$info = parse_url (urldecode ($_SERVER['REQUEST_URI']));
	$path = array_pop (preg_split ('/\/method.(copy|delete|edit|get|list|mkdir|move|put|search|lock|unlock|statuses|access-levels|teams)/', $info['path'], 2));

	$path = rtrim ($path, '/');

	// check permissions
	if (! session_allowed ('sitellite_filesystem', 'rw', 'resource')) {
		return webfiles_error (403, 'Forbidden');
	}

	// disallow .. references
	if (strpos ($path, '..') !== false) {
		return webfiles_error (403, 'Forbidden');
	}

	// only post requests contain a body
	if ($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'PUT') {
		return strtolower ($path);
	}

	// fetch the body and return array(path, body)
	$body = '';
	$stream = fopen ('php://input', 'r');
	while (! feof ($stream)) {
		$body .= fread ($stream, 4096);
	}
	fclose ($stream);

	$path = strtolower ($path);

	return array ($path, $body);
}

// generates an error response and exists
function webfiles_error ($code, $msg) {
	header ('HTTP/1.1 ' . $code . ' ' . $msg);
	header ('Content-Type: application/json');
	$data = json ((object) array (
		'code' => $code,
		'msg' => $msg,
	));
	header ('Content-Length: ' . strlen ($data));
	echo $data;
	exit;
}

// sends a response data structure as json
function webfiles_response ($data) {
	header ('HTTP/1.1 200 OK');
	header ('Content-Type: application/json');
	$data = json ($data);
	header ('Content-Length: ' . strlen ($data));
	echo $data;
	exit;
}

// retrieves lock info for the specified file or folder
function webfiles_lock ($path) {
	$path = trim ($path, '/');
	while (! empty ($path)) {
		$info = lock_info ('sitellite_filesystem', $path);
		if (is_object ($info)) {
			return (object) array (
				'owner' => $info->user,
				'expires' => $info->expires,
			);
		}
		$parts = pathinfo ($path);
		$path = $parts['dirname'];
		if ($parts['dirname'] == '.' || empty ($parts['dirname'])) {
			$path = '';
		}
	}
	return false;
}

// must be called with a changelog, but the key is optional
function webfiles_workflow ($action, $key, $file, $changelog) {
	if (is_object ($file)) {
		$file = (array) $file;
	}
	ob_start ();
	Workflow::trigger (
		$action,
		array (
			'collection' => 'sitellite_filesystem',
			'key' => $key,
			'data' => $file,
			'changelog' => $changelog,
			'message' => 'Collection: sitellite_filesystem, Item: ' . $file['name'],
		)
	);
	ob_end_clean ();
}

$rex = new Rex ('sitellite_filesystem');
$prefix = 'inc/data';
lock_init ();
umask (0000);

switch ($parameters['method']) {





	case 'copy':
		$path = webfiles_request ();
		$info = $rex->getCurrent (ltrim ($path, '/'));
		if (! $info) {
			webfiles_error (404, 'Not found');
		}
		$info2 = clone ($info);
		unset ($info2->body);
		unset ($info2->name);
		if (! session_allowed ($info2, 'r')) {
			webfiles_error (403, 'Forbidden');
		}

		$parts = pathinfo ($path);
		$parts['basename'] = preg_replace ('/\.' . preg_quote ($parts['extension']) . '$/', '', $parts['basename']);
		$new_name = $parts['dirname'] . '/' . $parts['basename'] . ' copy' . '.' . $parts['extension'];
		if (empty ($parts['extension'])) {
			$new_name = rtrim ($new_name, '.');
		}
		$n = 2;
		while (file_exists ($prefix . $new_name)) {
			$new_name = $parts['dirname'] . '/' . $parts['basename'] . ' copy ' . $n . '.' . $parts['extension'];
			if (empty ($parts['extension'])) {
				$new_name = rtrim ($new_name, '.');
			}
			$n++;
		}

		$info->name = ltrim ($new_name, '/');
		$size = $info->filesize;
		unset ($info->filesize);
		unset ($info->last_modified);
		unset ($info->date_created);
		$info->sitellite_status = 'draft';
		$info->sitellite_access = 'private';
		$info->sitellite_team = session_team ();
		$info->sitellite_owner = session_username ();

		// todo: copy directories

		$res = $rex->create ((array) $info, 'Duplicated from ' . $path);
		if (! $res) {
			webfiles_error (409, $rex->error);
		}

		chmod ($prefix . $new_name, 0777);

		webfiles_workflow ('add', $info->name, $info, 'Duplicated from ' . $path . ' via the Sitellite Desktop');

		webfiles_response ((object) array (
			'type' => mime ($new_name),
			'name' => $new_name,
			'size' => $size,
			'created' => date ('Y-m-d H:i:s'),
			'modified' => date ('Y-m-d H:i:s'),
			'keywords' => $info->keywords,
			'description' => $info->description,
			'access' => $info->sitellite_access,
			'status' => $info->sitellite_status,
			'team' => $info->sitellite_team,
			'owner' => $info->sitellite_owner,
			'lock' => webfiles_lock ($new_name),
		));

		break;





	case 'delete':
		$path = webfiles_request ();
		$info = $rex->getCurrent (ltrim ($path, '/'));
		if (! $info) {
			webfiles_error (404, 'Not found');
		}
		$info2 = clone ($info);
		unset ($info2->body);
		unset ($info2->name);
		if (! session_allowed ($info2, 'rw')) {
			webfiles_error (403, 'Forbidden');
		}

		$lock = webfiles_lock ($path);
		if ($lock && $lock->owner != session_username ()) {
			webfiles_error (409, 'Conflict');
		}

		// todo: delete directories

		$res = $rex->delete (ltrim ($path, '/'));
		if (! $res) {
			webfiles_error (500, $rex->error);
		}

		webfiles_workflow ('delete', $info->name, $info, 'Deleted via the Sitellite Desktop');

		webfiles_response (true);

		break;





	case 'edit':
		list ($path, $body) = webfiles_request ();
		$info = $rex->getCurrent (ltrim ($path, '/'));
		if (! $info) {
			webfiles_error (404, 'Not found');
		}
		$info2 = clone ($info);
		unset ($info2->body);
		unset ($info2->name);
		if (! session_allowed ($info2, 'rw')) {
			webfiles_error (403, 'Forbidden');
		}

		$lock = webfiles_lock ($path);
		if ($lock && $lock->owner != session_username ()) {
			webfiles_error (409, 'Conflict');
		}

		// parse the json data
		loader_import ('pear.Services.JSON');
		$json = new Services_JSON ();
		$data = $json->decode ($body);

		// compile the changes
		$vals = array ();
		foreach ($data as $k => $v) {
			switch ($k) {
				case 'access':
				case 'status':
				case 'team':
				case 'owner':
					$vals['sitellite_' . $k] = $v;
					break;
				case 'keywords':
				case 'description':
				case 'display_title':
					$vals[$k] = $v;
					break;
				case 'name':
					$parts = pathinfo ($path);
					$vals[$k] = ltrim ($parts['dirname'] . '/' . $v, '/');
					break;
			}
		}

		if (isset ($vals['sitellite_status'])) {
			$new_status = $vals['sitellite_status'];
		} else {
			$new_status = $info->sitellite_status;
		}

		// verify $vals['name'] doesn't already exist if it's a rename
		if (isset ($vals['name']) && $vals['name'] != ltrim ($path, '/') && file_exists ($prefix . '/' . $vals['name'])) {
			webfiles_error (409, 'Conflict');
		}

		// rename folders
		//if (@is_dir (rtrim ($prefix . $path, '/'))) {
			// 1. build duplicate directory structure
			// 2. move all files to new structure
			// 3. delete now-empty old directory structure
		//}

		$method = $rex->determineAction (ltrim ($path, '/'), $new_status);
		$res = $rex->{$method} (ltrim ($path, '/'), $vals);
		if (! $res) {
			webfiles_error (500, $rex->error);
		}

		if (isset ($vals['name'])) {
			$info = $rex->getCurrent ($vals['name']);
		} else {
			$info = $rex->getCurrent (ltrim ($path, '/'));
		}

		webfiles_workflow ('edit', ltrim ($path, '/'), $info, 'Edited via the Sitellite Desktop');

		webfiles_response ((object) array (
			'type' => mime ($info->name),
			'name' => '/' . $info->name,
			'size' => $info->filesize,
			'created' => $info->date_created,
			'modified' => $info->last_modified,
			'keywords' => $info->keywords,
			'description' => $info->description,
			'access' => $info->sitellite_access,
			'status' => $info->sitellite_status,
			'team' => $info->sitellite_team,
			'owner' => $info->sitellite_owner,
			'lock' => webfiles_lock ($info->name),
		));

		break;





	case 'get':
		$path = webfiles_request ();
		$info = $rex->getCurrent (ltrim ($path, '/'));
		if (! $info) {
			webfiles_error (404, 'Not found');
		}
		$info2 = clone ($info);
		unset ($info2->body);
		unset ($info2->name);
		if (! session_allowed ($info2, 'rw')) {
			webfiles_error (403, 'Forbidden');
		}
		header ('HTTP/1.1 200 OK');
		header ('Content-Type: ' . mime ($path));
		header ('Content-Length: ' . strlen ($info->body));
		header ('Content-Disposition: attachment; filename=' . basename ($path));
		echo $info->body;
		exit;
		break;





	case 'list':
		$path = webfiles_request ();
		if (! is_dir ($prefix . $path)) {
			webfiles_error (404, 'Not found');
		}

		$obj = new StdClass;
		$obj->root = $path . '/';
		$obj->files = array ();
		$folder_list = array ();
		$file_list = array ();

		$files = Dir::fetch ($prefix . $path, true);

		foreach ($files as $file) {
			if ($file == 'CVS') {
				continue;
			}
			if (is_dir ($prefix . $path . '/' . $file)) {
				$folder_list[] = (object) array (
					'type' => 'httpd/unix-directory',
					'name' => $path . '/' . $file,
					'created' => filectime ($prefix . $path . '/' . $file),
					'modified' => filemtime ($prefix . $path . '/' . $file),
					'lock' => webfiles_lock ($path . '/' . $file),
				);
			} else {
				$info = $rex->getCurrent (ltrim ($path . '/' . $file, '/'));
				unset ($info->name);
				unset ($info->body);
				if (! session_allowed ($info, 'rw')) {
					continue;
				}

				$file_list[] = (object) array (
					'type' => mime ($file),
					'name' => $path . '/' . $file,
					'size' => $info->filesize,
					'created' => $info->date_created,
					'modified' => $info->last_modified,
					'keywords' => $info->keywords,
					'description' => $info->description,
					'access' => $info->sitellite_access,
					'status' => $info->sitellite_status,
					'team' => $info->sitellite_team,
					'owner' => $info->sitellite_owner,
					'lock' => webfiles_lock ($path . '/' . $file),
				);
			}
		}

		$obj->files = array_merge ($folder_list, $file_list);

		webfiles_response ($obj);
		break;





	case 'mkdir':
		$path = webfiles_request ();
		$res = Dir::build ($prefix . $path, 0777);
		if (! $res) {
			webfiles_error (500, 'Internal server error');
		}
		webfiles_response ((object) array (
			'type' => 'httpd/unix-directory',
			'name' => $path,
			'created' => date ('Y-m-d H:i:s'),
			'modified' => date ('Y-m-d H:i:s'),
		));
		break;





	case 'move':
		list ($path, $move_to) = webfiles_request ();

		$path = trim (str_replace ('//', '/', $path), '/');
		$move_to = trim (str_replace ('//', '/', $move_to), '/');

		if (! file_exists ($prefix . '/' . $path)) {
			webfiles_error (404, 'Not found');
		}

		$lock = webfiles_lock ($path);
		if ($lock && $lock->owner != session_username ()) {
			webfiles_error (409, 'Conflict');
		}

		$info = $rex->getCurrent ($path);
		if (! $info) {
			webfiles_error (404, 'Not found');
		}
		$info2 = clone ($info);
		unset ($info2->body);
		unset ($info2->name);
		if (! session_allowed ($info2, 'rw')) {
			webfiles_error (403, 'Forbidden');
		}

		if (! is_dir ($prefix . '/' . $move_to)) {
			webfiles_error (500, 'Directory doesn\'t exist');
		}

		$new_file = $move_to . '/' . basename ($path);
		if (file_exists ($prefix . '/' . $new_file)) {
			webfiles_error (409, 'Conflict');
		}

		unset ($info->body);
		unset ($info->filesize);
		unset ($info->last_modified);
		unset ($info->date_created);
		$info->name = $new_file;

		$method = $rex->determineAction ($path, $info->sitellite_status);
		$res = $rex->{$method} ($path, (array) $info);
		if (! $res) {
			webfiles_error (500, $rex->error);
		}

		$info = $rex->getCurrent ($new_file);

		// update locks
		if ($lock) {
			lock_remove ('sitellite_filesystem', $path);
			lock_add ('sitellite_filesystem', $new_file);
		}

		webfiles_workflow ('edit', $path, $info, 'Moved via the Sitellite Desktop');

		webfiles_response ((object) array (
			'type' => mime ($new_file),
			'name' => '/' . $new_file,
			'size' => $info->filesize,
			'created' => $info->date_created,
			'modified' => $info->last_modified,
			'keywords' => $info->keywords,
			'description' => $info->description,
			'access' => $info->sitellite_access,
			'status' => $info->sitellite_status,
			'team' => $info->sitellite_team,
			'owner' => $info->sitellite_owner,
			'lock' => webfiles_lock ($new_file),
		));

		break;





	case 'put':
		list ($path, $body) = webfiles_request ();

		$lock = webfiles_lock ($path);
		if ($lock && $lock->owner != session_username ()) {
			webfiles_error (409, 'Conflict');
		}

		$info = $rex->getCurrent (ltrim ($path, '/'));
		if ($info) {
			// overwrite
			$info2 = clone ($info);
			unset ($info2->body);
			unset ($info2->name);
			if (! session_allowed ($info2, 'rw')) {
				webfiles_error (403, 'Forbidden');
			}

			$method = $rex->determineAction (ltrim ($path, '/'), $info->sitellite_status);
			$res = $rex->{$method} (ltrim ($path, '/'), array ('body' => $body));
			if (! $res) {
				webfiles_error (500, $rex->error);
			}

			$info = $rex->getCurrent (ltrim ($path, '/'));

			webfiles_response ((object) array (
				'type' => mime ($path),
				'name' => $path,
				'size' => $info->filesize,
				'created' => $info->date_created,
				'modified' => $info->last_modified,
				'keywords' => $info->keywords,
				'description' => $info->description,
				'access' => $info->sitellite_access,
				'status' => $info->sitellite_status,
				'team' => $info->sitellite_team,
				'owner' => $info->sitellite_owner,
				'lock' => webfiles_lock ($path),
			));
		}

		$res = $rex->create (array (
			'name' => ltrim ($path, '/'),
			'display_title' => basename ($path),
			'keywords' => '',
			'description' => '',
			'sitellite_status' => 'draft',
			'sitellite_access' => 'private',
			'sitellite_owner' => session_username (),
			'sitellite_team' => session_team (),
			'body' => $body
		));
		if (! $res) {
			webfiles_error (500, $rex->error);
		}

		$info = $rex->getCurrent (ltrim ($path, '/'));

		webfiles_workflow ('add', ltrim ($path, '/'), $info, 'Uploaded via the Sitellite Desktop');

		webfiles_response ((object) array (
			'type' => mime ($path),
			'name' => $path,
			'size' => $info->filesize,
			'created' => $info->date_created,
			'modified' => $info->last_modified,
			'keywords' => $info->keywords,
			'description' => $info->description,
			'access' => $info->sitellite_access,
			'status' => $info->sitellite_status,
			'team' => $info->sitellite_team,
			'owner' => $info->sitellite_owner,
			'lock' => webfiles_lock ($path),
		));

		break;





	case 'search':
		$path = webfiles_request ();

		// assign query to cgi object and rex will see it
		// automatically, that's how awesome rex is
		// (actually, keywords is the predefined text
		// search facet which searches the name, display_title,
		// keywords and description, but not the body)
		// future: use sitesearch for body searching?
		global $cgi;
		$cgi->_keywords = $parameters['query'];

		// we'll add a few more checks for permissions to the search params as well
		$params = array ();

		$acl_list = session_allowed_access_list ();
		if (! in_array ('all', $acl_list)) {
			$params['sitellite_access'] = new rList (
				'sitellite_access',
				session_allowed_access_list ()
			);
		}

		$team_list = session_allowed_teams_list ();
		if (! in_array ('all', $team_list)) {
			$params['sitellite_team'] = new rList (
				'sitellite_team',
				$team_list
			);
		}

		// perform the search
		$res = $rex->getStoreList ($params);

		// build the result object
		$out = new StdClass;
		$out->query = $parameters['query'];
		$out->results = count ($res);
		$out->files = array ();

		foreach ($res as $row) {
			$info = $rex->getCurrent ($row->name);
			unset ($info->body);

			$out->files[] = (object) array (
				'type' => mime ($info->name),
				'name' => '/' . $info->name,
				'size' => $info->filesize,
				'created' => $info->date_created,
				'modified' => $info->last_modified,
				'keywords' => $info->keywords,
				'description' => $info->description,
				'access' => $info->sitellite_access,
				'status' => $info->sitellite_status,
				'team' => $info->sitellite_team,
				'owner' => $info->sitellite_owner,
				'lock' => webfiles_lock ($info->name),
			);
		}

		webfiles_response ($out);

		break;





	case 'lock':
		$path = webfiles_request ();

		if (! file_exists ($prefix . $path)) {
			webfiles_error (404, 'Not found');
		}

		//if (is_dir ($prefix . $path)) {
		//	webfiles_error (500, 'Locks not supported on directories');
		//}

		// it's someone else's lock
		$lock = webfiles_lock ($path);
		if ($lock && $lock->owner != session_username ()) {
			webfiles_error (409, 'Conflict');
		}

		//if (lock_exists ('sitellite_filesystem', ltrim ($path, '/'))) {
		//	webfiles_error (409, 'Conflict');
		//}

		if (! lock_add ('sitellite_filesystem', trim ($path, '/'))) {
			webfiles_error (500, 'Internal server error');
		}

		webfiles_response ((object) array (
			'owner' => session_username (),
			'expires' => date ('Y-m-d H:i:s', time () + 3600),
		));

		break;





	case 'unlock':
		$path = webfiles_request ();

		if (! file_exists ($prefix . $path)) {
			webfiles_error (404, 'Not found');
		}

		//if (is_dir ($prefix . $path)) {
		//	webfiles_error (500, 'Locks not supported on directories');
		//}

		// it's someone else's lock
		$lock = webfiles_lock ($path);
		if ($lock && $lock->owner != session_username ()) {
			webfiles_error (409, 'Conflict');
		}

		//if (lock_exists ('sitellite_filesystem', ltrim ($path, '/'))) {
		//	webfiles_error (409, 'Conflict');
		//}

		if (! lock_remove ('sitellite_filesystem', trim ($path, '/'))) {
			webfiles_error (500, 'Internal server error');
		}

		webfiles_response (true);

		break;





	case 'statuses':
		webfiles_response (session_get_statuses ());
		break;





	case 'access-levels':
		webfiles_response (session_get_access_levels ());
		break;





	case 'teams':
		$teams = session_allowed_teams_list ();
		if (in_array ('all', $teams)) {
			$teams = session_get_teams ();
		}
		sort ($teams);
		webfiles_response ($teams);
		break;
}

exit;

?>