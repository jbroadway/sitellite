--TEST--
saf.Database.Driver.PgSQL
--SKIPIF--
<?php

if (! extension_loaded ('pgsql')) {
	echo 'skip pgsql extension not available';
}

?>
--FILE--
<?php

include_once ('../init.php');

loader_import ('saf.Database');

// 1. constructor and object state

$db = new Database ('PgSQL:localhost:DBNAME', 'postgres', 'PASS');

var_dump (substr ($db->connection, 0, 8));

// 2. query()

$q =& $db->query ('create table pg_driver_test (id int, name char(16))');

var_dump (strtolower (get_class ($q)));

var_dump ($q->execute ());

// 3. insert

var_dump (db_execute ('insert into pg_driver_test (id, name) values (?, ?)', 1, 'test'));

// 4. select, db_fetch, db_fetch_array, db_single, db_shift, db_shift_array, db_pairs

var_dump (db_fetch ('select * from pg_driver_test'));
var_dump (db_fetch ('select * from nonexistent_table_name'));

var_dump (db_fetch_array ('select * from pg_driver_test'));
var_dump (db_fetch_array ('select * from nonexistent_table_name'));
var_dump (db_error ());

var_dump (db_single ('select * from pg_driver_test'));
var_dump (db_single ('select * from nonexistent_table_name'));

var_dump (db_shift ('select * from pg_driver_test'));
var_dump (db_shift ('select * from nonexistent_table_name'));

var_dump (db_shift_array ('select * from pg_driver_test'));
var_dump (db_shift_array ('select * from nonexistent_table_name'));

var_dump (db_pairs ('select * from pg_driver_test'));
var_dump (db_pairs ('select * from nonexistent_table_name'));

var_dump (db_execute ('drop table pg_driver_test'));

?>
--EXPECT--
string(8) "Resource"
string(11) "pgsql_query"
bool(true)
bool(true)
object(stdClass)(2) {
  ["id"]=>
  string(1) "1"
  ["name"]=>
  string(4) "test"
}
bool(false)
array(1) {
  [0]=>
  object(stdClass)(2) {
    ["id"]=>
    string(1) "1"
    ["name"]=>
    string(4) "test"
  }
}
array(0) {
}
object(stdClass)(2) {
  ["id"]=>
  string(1) "1"
  ["name"]=>
  string(4) "test"
}
bool(false)
string(1) "1"
bool(false)
array(1) {
  [0]=>
  string(1) "1"
}
array(0) {
}
array(1) {
  [1]=>
  string(4) "test"
}
array(0) {
}
bool(true)
