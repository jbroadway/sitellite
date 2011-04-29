--TEST--
saf.Database
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.Database');

// constructor method

$database = new Database ('$connstr', '$user', '$pass', '$persistent');

// query() method

var_dump ($database->query ('$sql', '$cache'));

// table() method

var_dump ($database->table ('$table', '$pkey'));

// abstractSql() method

var_dump ($database->abstractSql ('$path'));

// fetch() method

var_dump ($database->fetch ());

// single() method

var_dump ($database->single ());

// shift() method

var_dump ($database->shift ());

// execute() method

var_dump ($database->execute ());

// connect() method

var_dump ($database->connect ());

// close() method

var_dump ($database->close ());

// getTables() method

var_dump ($database->getTables ());

// tableExists() method

var_dump ($database->tableExists ('$tbl'));

// setFetchMode() method

var_dump ($database->setFetchMode ('$mode'));

// getFetchMode() method

var_dump ($database->getFetchMode ());

// getAll() method

var_dump ($database->getAll ('$sql'));

// quote() method

var_dump ($database->quote ('$string'));

// setOption() method

var_dump ($database->setOption ('$option', '$val'));

// createSequence() method

var_dump ($database->createSequence ('$seq_name'));

// dropSequence() method

var_dump ($database->dropSequence ('$seq_name'));

// getSequenceName() method

var_dump ($database->getSequenceName ('$sqn'));

// nextId() method

var_dump ($database->nextId ('$seq_name'));

// db_query() function

var_dump (db_query ('$sql', '$cache'));

// db_table() function

var_dump (db_table ('$table', '$pkey'));

// db_fetch() function

var_dump (db_fetch ());

// db_fetch_array() function

var_dump (db_fetch_array ());

// db_shift_array() function

var_dump (db_shift_array ());

// db_pairs() function

var_dump (db_pairs ());

// db_execute() function

var_dump (db_execute ());

// db_single() function

var_dump (db_single ());

// db_shift() function

var_dump (db_shift ());

// db_rows() function

var_dump (db_rows ());

// db_lastid() function

var_dump (db_lastid ());

// db_error() function

var_dump (db_error ());

// db_err_sql() function

var_dump (db_err_sql ());

// db_quote() function

var_dump (db_quote ('$string'));

// db_create_sequence() function

var_dump (db_create_sequence ('$sqn'));

// db_drop_sequence() function

var_dump (db_drop_sequence ('$sqn'));

// db_next_id() function

var_dump (db_next_id ('$sqn'));

// db_get_sequence_name() function

var_dump (db_get_sequence_name ('$sqn'));

// db_fetch_mode() function

var_dump (db_fetch_mode ('$mode'));

// db_pear_emu() function

var_dump (db_pear_emu ('$bool'));

?>
--EXPECT--
