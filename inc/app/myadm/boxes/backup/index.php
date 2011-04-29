<?php

global $cgi;

if (! $cgi->format) {
	$cgi->format = 'sql';
}

if (is_array ($cgi->table)) {
	$tables = $cgi->table;
} elseif (! empty ($cgi->table)) {
	$tables = array ($cgi->table);
} else {
	$tables = db_shift_array ('show tables');
}

if (count ($tables) == 1) {
	$fn = $tables[0];
} else {
	$fn = conf ('Database', 'database');
}

//info ($tables);
//exit;

set_time_limit (0);

header ('Cache-control: private');
header ('Content-Type: text/plain');
header ('Content-Disposition: attachment; filename=' . $fn . '-' . date ('Y-m-d') . '.' . $cgi->format);

foreach ($tables as $table) {
	if ($cgi->format == 'csv') {
		echo '----- table: ' . $table . " -----\n";
		$q = db_query ('select * from ' . $table);
		$q->execute ();
		$headers = false;
		while ($row = $q->fetch ()) {
			if (! $headers) {
				echo join (',', array_keys ((array) $row)) . "\n";
				$headers = true;
			}
			$r = (array) $row;
			foreach (array_keys ($r) as $k) {
				$r[$k] = str_replace ('"', '""', $r[$k]);
				if (strpos ($r[$k], ',') !== false) {
					$r[$k] = '"' . $r[$k] . '"';
				}
			}
			echo str_replace (array ("\r", "\n"), array ('\\r', '\\n'), join (',', $r)) . "\n";
		}
	} else {
		echo "\n----- table: " . $table . " -----\n\n";

		$fields = db_fetch_array ('describe ' . $table);
		$create = 'create table ' . $table . " (\n";
		$index = array ();
		foreach ($fields as $k => $field) {
			$arr = (array) $field;
			$name = array_shift ($arr);
			$type = array_shift ($arr);
			$null = array_shift ($arr);
			$key = array_shift ($arr);
			$default = array_shift ($arr);
			$extra = array_shift ($arr);

			$create .= "\t" . $name . ' ' . $type;
			if ($null != 'YES') {
				$create .= ' not null';
			}
			if (! empty ($default)) {
				$create .= ' default "' . $default . '"';
			}
			if (! empty ($extra)) {
				$create .= ' ' . $extra;
			}
			switch ($key) {
				case 'PRI':
					$create .= ' primary key';
					break;
				case 'MUL':
					$index[] = $name;
					break;
				default:
					break;
			}
			if ($k < count ($fields) - 1 || count ($index) > 0) {
				$create .= ',';
			}
			$create .= "\n";
		}
		if (count ($index) > 0) {
			$create .= "\tindex (" . join (', ', $index) . ")\n";
		}
		$create .= ");\n\n";
		echo $create;

		$q = db_query ('select * from ' . $table);
		$q->execute ();
		while ($row = $q->fetch ()) {
			$insert = 'insert into ' . $table . ' values (';
			$sep = '';
			foreach ((array) $row as $value) {
				$insert .= $sep . db_quote ($value);
				$sep = ', ';
			}
			$insert .= ");\n";
			echo $insert;
		}
	}
}

exit;

?>