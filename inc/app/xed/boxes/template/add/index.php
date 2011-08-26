<?php

if (! session_admin ()) {
	exit;
}

if (! empty ($parameters['name']) && ! empty ($parameters['body'])) {
	if (db_shift ('select count(*) from xed_templates where name = ?', $parameters['name'])) {
		db_execute (
			'update xed_templates set body = ? where name = ?',
			$parameters['body'],
			$parameters['name']
		);
	} else {
		db_execute (
			'insert into xed_templates values (null, ?, ?)',
			$parameters['name'],
			$parameters['body']
		);
	}
}

loader_import ('saf.Misc.RPC');

echo rpc_response (true);

exit;

?>