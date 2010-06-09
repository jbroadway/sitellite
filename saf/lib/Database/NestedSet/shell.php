<?php

/**
 * @package Database
 */

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

include_once ('../../../init.php');

loader_import ('saf.Database');
loader_import ('saf.Database.NestedSet');

$conf = parse_ini_file ('../../../../inc/conf/config.ini.php', true);
$db = new Database ('MySQL:' . $conf['Database']['hostname'] . ':' . $conf['Database']['database'], $conf['Database']['username'], $conf['Database']['password']);

$ns = false;
$collection = false;
$_fields = array ();
$_roots = array ();
$t = true;

while ($t) {
	echo 'nestedset> ';
	$input = explode (' ', trim (fgets (STDIN)));

	switch ($input[0]) {
		case 'about':
			echo "NestedSet shell is an interactive querying and testing interface\n";
			echo "to the saf.Database.NestedSet package.  It allows you to create,\n";
			echo "modify, and inspect NestedSet collection.\n\n";
			echo "License:     GPL\n";
			echo "Author:      Lux (Sitellite.org)\n";
			echo "Feedback:    john.luxford@gmail.com\n";
			echo "Web Site(s): http://www.sitellite.org/\n";
			break;
		case 'help':
			echo "Command List\n------------\n\n";
			echo "Miscellaneous:\n";
			echo "help                            Display this list of commands.\n";
			echo "about                           Description of this application.\n";
			echo "quit                            Disconnect and exit.\n";
			echo "\n";
			echo "Editing:\n";
			echo "create collection [fields]      Create a new collection.\n";
			echo "drop                            Drop the current collection.\n";
			echo "clear                           Delete all nodes from the current collection.\n";
			echo "addRoot id [fields]             Add a new root node.\n";
			echo "add parent id [fields]          Add a new node under the specified parent node.\n";
			echo "edit id [fields]                Modify the specified node.\n";
			echo "move id newParentId             Move the specified node.\n";
			echo "rename id newId                 Rename the specified node.\n";
			echo "delete id                       Delete the specified node.\n";
			echo "deleteBranch id                 Delete the specified node and all nodes below it.\n";
			echo "\n";
			echo "Inspection:\n";
			echo "use collection                  Select a collection.\n";
			echo "root                            Display the current root node.\n";
			echo "root id                         Set the current root node.\n";
			echo "inspect variable                Dump the contents of the specified variable.\n";
			echo "dump                            Display the entire collection.\n";
			echo "get id                          Display a single node.\n";
			echo "parent id                       Display the parent of the specified node.\n";
			echo "isParent parent id              Determine whether one node is a parent of another.\n";
			echo "path id                         Display the path to the specified node.\n";
			echo "tree                            Display the collection hierarchy.\n";
			echo "children id                     Display the direct children of the specified node.\n";
			echo "branch id                       Display the tree starting from the specified node.\n";
			echo "siblings id                     Display the siblings of the specified node.\n";
			echo "roots                           Display a list of root nodes.\n";
			echo "find key=value key2=value2      Search the collection for the specified query.\n";
			break;
		case 'quit':
			echo "Goodbye.\n";
			$t = false;
			break;
		case 'use':
			if (! $input[1]) {
				echo "Error: no collection specified.\n";
				break;
			}
			array_shift ($input);
			$collection = array_shift ($input);
			
			$ns = new NestedSet ($collection);

			$table =& db_table ($collection);
			$table->getInfo ();
			$_fields = array ();
			foreach ($table->columns as $v) {
				if (strpos ($v->name, 'ns_') !== 0) {
					$_fields[] = $v->name;
				}
			}

			$roots = $ns->getRoots ();
			if (count ($roots) > 0) {
				$first = true;
				foreach ($roots as $root) {
					if ($first) {
						$ns->root ($root->id);
						$first = false;
					}
					$_roots[] = $root->id;
				}
			}
			break;

		case 'create':
			if (! $input[1]) {
				echo "Error: no collection specified.\n";
				break;
			}

			array_shift ($input);
			$collection = array_shift ($input);
			$_fields = array ('id');
			$fields = array ();
			foreach ($input as $field) {
				$fields[$field] = 'char(32)';
				$_fields[] = $field;
			}
			$ns = new NestedSet ($collection);
			$res = $ns->create ($fields);
			if (! $res) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			echo "Collection created.\n";
			break;

		case 'drop':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			}

			echo "Are you sure you want to drop the collection '" . $collection . "'? (yes|no)\n";	
			$in = explode (' ', trim (fgets (STDIN)));
			if ($in[0] != 'yes') {
				echo "Aborted.\n";
				break;
			}
			db_execute ('drop table ' . $collection);
			echo "Collection dropped.\n";

			break;

		case 'clear':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			}

			echo "Are you sure you want to clear the collection '" . $collection . "'? (yes|no)\n";	
			$in = explode (' ', trim (fgets (STDIN)));
			if ($in[0] != 'yes') {
				echo "Aborted.\n";
				break;
			}
			db_execute ('delete from ' . $collection);
			echo "Collection cleared.\n";

			break;

		case 'root':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			}

			if ($input[1]) {
				$ns->root ($input[1]);
			} else {
				echo $ns->root () . "\n";
			}
			break;

		case 'addRoot':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no root ID specified.\n";
				break;
			}

			array_shift ($input);
			$fields = array ();
			foreach ($input as $k => $v) {
				$fields[$_fields[$k]] = $v;
			}
			$res = $ns->addRoot ($fields);
			if (! $res) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			echo "Root added:\n";
			foreach ($fields as $k => $v) {
				echo str_pad (substr ($k, 0, 12), 16) . $v . "\n";
			}
			break;

		case 'add':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no parent ID specified.\n";
				break;
			} elseif (! $input[2]) {
				echo "Error: no node ID specified.\n";
				break;
			}

			array_shift ($input);
			$pid = array_shift ($input);
			$fields = array ();
			foreach ($input as $k => $v) {
				$fields[$_fields[$k]] = $v;
			}
			$res = $ns->add ($pid, $fields);
			if (! $res) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			echo "Node added:\n";
			foreach ($fields as $k => $v) {
				echo str_pad (substr ($k, 0, 12), 16) . $v . "\n";
			}
			break;

		case 'edit':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no node ID specified.\n";
				break;
			} elseif (! $input[2]) {
				echo "Error: no changes specified.\n";
				break;
			}

			array_shift ($input);
			$nid = array_shift ($input);
			$fields = array ();
			foreach ($input as $k => $v) {
				$fields[$_fields[$k]] = $v;
			}
			$res = $ns->edit ($nid, $fields);
			if (! $res) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			echo "Node saved:\n";
			foreach ($fields as $k => $v) {
				echo str_pad (substr ($k, 0, 12), 16) . $v . "\n";
			}
			break;

		case 'move':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no node ID specified.\n";
				break;
			} elseif (! $input[2]) {
				echo "Error: no new parent ID specified.\n";
				break;
			}

			array_shift ($input);
			$id = array_shift ($input);
			$np = array_shift ($input);
			$res = $ns->move ($id, $np);
			if (! $res) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			echo "Node moved.\n";
			break;

		case 'rename':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no node ID specified.\n";
				break;
			} elseif (! $input[2]) {
				echo "Error: no new node ID specified.\n";
				break;
			}

			array_shift ($input);
			$id = array_shift ($input);
			$new = array_shift ($input);
			$res = $ns->rename ($id, $new);
			if (! $res) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			echo "Node renamed.\n";
			break;

		case 'delete':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no node ID specified.\n";
				break;
			}

			echo "Are you sure you want to delete the node '" . $input[1] . "'? (yes|no)\n";	
			$in = explode (' ', trim (fgets (STDIN)));
			if ($in[0] != 'yes') {
				echo "Aborted.\n";
				break;
			}
			$res = $ns->delete ($input[1]);
			if (! $res) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			echo "Node deleted.\n";

			break;

		case 'deleteBranch':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no node ID specified.\n";
				break;
			}

			echo "Are you sure you want to delete the branch '" . $input[1] . "'? (yes|no)\n";	
			$in = explode (' ', trim (fgets (STDIN)));
			if ($in[0] != 'yes') {
				echo "Aborted.\n";
				break;
			}
			$res = $ns->delete ($input[1], true);
			if (! $res) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			echo "Branch deleted.\n";

			break;

		case 'inspect':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no variable specified.\n";
				break;
			}

			var_dump (${$input[1]});

			break;

		case 'dump':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			}

			$res = db_fetch_array ('select * from ' . $collection);
			$first = true;
			foreach ($res as $row) {
				if ($first) {
					foreach (get_object_vars ($row) as $k => $v) {
						echo str_pad (strtoupper (substr ($k, 0, 12)), 12);
					}
					echo "\n";
					$first = false;
				}
				foreach (get_object_vars ($row) as $k => $v) {
					echo str_pad (substr ($v, 0, 12), 12);
				}
				echo "\n";
			}
			break;

		case 'get':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no node ID specified.\n";
				break;
			}

			$row = db_single ('select * from ' . $collection . ' where id = ?', $input[1]);
			if (! $row) {
				echo "Error: node not found.\n";
				break;
			}
			foreach (get_object_vars ($row) as $k => $v) {
				echo str_pad (strtoupper (substr ($k, 0, 12)), 12);
			}
			echo "\n";
			foreach (get_object_vars ($row) as $k => $v) {
				echo str_pad (substr ($v, 0, 12), 12);
			}
			echo "\n";
			break;

		case 'parent':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no node ID specified.\n";
				break;
			}

			$res = $ns->parent ($input[1]);
			if (! $res) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			echo $res->id . "\n";
			break;

		case 'isParent':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no parent ID specified.\n";
				break;
			} elseif (! $input[2]) {
				echo "Error: no node ID specified.\n";
				break;
			}

			if (! $ns->isParent ($input[1], $input[2])) {
				echo "no\n";
				break;
			}
			echo "yes\n";
			break;

		case 'path':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no node ID specified.\n";
				break;
			}

			$path = $ns->path ($input[1]);
			if (! $path) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			$cat = '';
			foreach ($path as $row) {
				echo $cat . $row->id;
				$cat = ' / ';
			}
			echo $cat . $input[1] . "\n";
			break;

		case 'tree':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			}

			$tree = $ns->children ($ns->root (), true);
			if (! $tree) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			echo $ns->root () . "\n";
			foreach ($tree as $row) {
				for ($i = 1; $i < $row->ns_level; $i++) {
					echo '-';
				}
				echo $row->id . "\n";
			}
			break;

		case 'children':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no node ID specified.\n";
				break;
			}

			$tree = $ns->children ($input[1]);
			if (! $tree) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			foreach ($tree as $row) {
				echo $row->id . "\n";
			}
			break;

		case 'branch':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no node ID specified.\n";
				break;
			}

			$tree = $ns->children ($input[1], true);
			if (! $tree) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			echo $input[1] . ":\n";
			foreach ($tree as $row) {
				for ($i = 1; $i < $row->ns_level; $i++) {
					echo '-';
				}
				echo $row->id . "\n";
			}
			break;

		case 'siblings':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			} elseif (! $input[1]) {
				echo "Error: no node ID specified.\n";
				break;
			}

			$tree = $ns->siblings ($input[1]);
			if (! $tree) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			foreach ($tree as $row) {
				echo $row->id . "\n";
			}
			break;

		case 'roots':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			}

			$tree = $ns->getRoots ();
			if (! $tree) {
				echo 'Error: ' . $ns->error . "\n";
				break;
			}
			foreach ($tree as $row) {
				echo $row->id . "\n";
			}
			break;

		case 'find':
			if (! $collection) {
				echo "Error: no collection in use.\n";
				break;
			}

			array_shift ($input);
			$query = array ();
			$lim = 0;
			$offset = 0;
			$ord = false;
			$sort = 'ASC';
			$count = false;
			foreach ($input as $in) {
				list ($k, $op, $v) = preg_split ('/(=|\>|\<)/', $in, -1, PREG_SPLIT_DELIM_CAPTURE);
				if ($k == 'limit') {
					$lim = $v;
				} elseif ($k == 'offset') {
					$offset = $v;
				} elseif ($k == 'orderBy') {
					$ord = $v;
				} elseif ($k == 'sort') {
					$sort = $v;
				} elseif ($k == 'count') {
					$count = true;
				} else {
					$query[] = $k . ' ' . $op . ' ' . db_quote ($v);
				}
			}
			$res = $ns->find ($query, array (), $lim, $offset, $ord, $sort, $count);
			if (! $res) {
				if ($ns->error) {
					echo 'Error: ' . $ns->error . "\n";
					break;
				}
				echo "No matches.\n";
				break;
			}
			if ($count) {
				echo $res . " matches.\n";
				break;
			}
			echo count ($res) . " matches:\n";
			foreach ($res as $row) {
				echo $row->id . "\n";
			}
			break;

		default:
			echo "Unknown command.  Try 'help' for command list.\n";
			break;
	}
}

exit;

?>