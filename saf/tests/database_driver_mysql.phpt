--TEST--
saf.Database.Driver.MySQL
--FILE--
<?php

include_once ('../init.php');

loader_import ('saf.Database');

// 1. constructor and object state

$db = new Database ('MySQL:localhost:DBNAME', 'USER', 'PASS');

var_dump (substr ($db->connection, 0, 8));

// 2. query()

$q =& $db->query ('create table my_driver_test (id int, name char(16))');

var_dump (strtolower (get_class ($q)));

var_dump ($q->execute ());

// 3. insert

var_dump (db_execute ('insert into my_driver_test (id, name) values (?, ?)', 1, 'test'));

// 4. select, db_fetch, db_fetch_array, db_single, db_shift, db_shift_array, db_pairs

var_dump (db_fetch ('select * from my_driver_test'));
var_dump (db_fetch ('select * from nonexistent_table_name'));
var_dump (db_error ());

var_dump (db_fetch_array ('select * from my_driver_test'));
var_dump (db_fetch_array ('select * from nonexistent_table_name'));
var_dump (db_error ());

var_dump (db_single ('select * from my_driver_test'));
var_dump (db_single ('select * from nonexistent_table_name'));
var_dump (db_error ());

var_dump (db_shift ('select * from my_driver_test'));
var_dump (db_shift ('select * from nonexistent_table_name'));
var_dump (db_error ());

var_dump (db_shift_array ('select * from my_driver_test'));
var_dump (db_shift_array ('select * from nonexistent_table_name'));
var_dump (db_error ());

var_dump (db_pairs ('select * from my_driver_test'));
var_dump (db_pairs ('select * from nonexistent_table_name'));
var_dump (db_error ());

var_dump (db_execute ('drop table my_driver_test'));

?>
--EXPECT--
string(8) "Resource"
string(11) "mysql_query"
bool(true)
bool(true)
object(stdClass)(2) {
  ["id"]=>
  string(1) "1"
  ["name"]=>
  string(4) "test"
}
bool(false)
string(51) "Table 'dbname.nonexistent_table_name' doesn't exist"
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
string(51) "Table 'dbname.nonexistent_table_name' doesn't exist"
object(stdClass)(2) {
  ["id"]=>
  string(1) "1"
  ["name"]=>
  string(4) "test"
}
bool(false)
string(51) "Table 'dbname.nonexistent_table_name' doesn't exist"
string(1) "1"
bool(false)
string(51) "Table 'dbname.nonexistent_table_name' doesn't exist"
array(1) {
  [0]=>
  string(1) "1"
}
array(0) {
}
string(51) "Table 'dbname.nonexistent_table_name' doesn't exist"
array(1) {
  [1]=>
  string(4) "test"
}
array(0) {
}
string(51) "Table 'dbname.nonexistent_table_name' doesn't exist"
bool(true)
