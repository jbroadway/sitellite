--TEST--
saf.Database.Generic
--INI--
error_reporting = E_ALL & ~E_NOTICE
--FILE--
<?php

// test setup

include_once ('../init.php');

loader_import ('saf.Database');
loader_import ('saf.Database.Generic');

$db = new Database ('MySQL:localhost:DBNAME', 'USER', 'PASS');

db_execute ('create table test_generic_one (
	id int not null auto_increment primary key,
	name char(10) not null
)');

class GenericOne extends Generic {
	function GenericOne () {
		parent::Generic ('test_generic_one', 'id');
	}
}

// testing...

// create a basic object and give it a name

$g = new GenericOne;
$g->set ('name', 'Test 1');
$g->save ();

// let's see if save() got us an id for our object

$id = $g->val ('id');
var_dump ($id);

// let's modify the name and get a fresh copy

$g->modify ($id, array ('name' => 'Test 2'));
print_r ($g->get ($id));

$g->remove ($id);
var_dump ($g->get ($id));

$g->add (array ('name' => 'Test 1'));
$g->add (array ('name' => 'Test 2'));
$g->add (array ('name' => 'Test 3'));
$g->add (array ('name' => 'Test 4'));
$g->add (array ('name' => 'Test 5'));

// return 2 items, offset by 1

$g->limit (2);
$g->offset (1);
$res = $g->find (array ());
var_dump ($res[0]->name);
var_dump (count ($res));
var_dump ($g->total);

$g->clear ();

// return all items, no offset

$res = $g->find (array ());
var_dump ($res[0]->name);
var_dump (count ($res));
var_dump ($g->total);

// get a single item

print_r ($g->single (array ()));

// get the id of the first item

var_dump ($g->shift (array ()));

// get just the count of a search

var_dump ($g->count (array ()));

// export an object from the class

print_r ($g->makeObj ());

// set a new current item

$res = $g->single (array ('name' => 'Test 2'));
$g->setCurrent ($res);
var_dump ($g->val ('name'));

// testing exists()

var_dump ($g->exists ($g->val ('id')));
var_dump ($g->exists (-1));

// testing validation

$g->addRule ('name', 'not empty', 'Name must not be empty');
$g->addRule ('name', 'length "10-"', 'Name must be 6 chars or less');
var_dump ($g->validate (array ('name' => ''))); // fail
var_dump ($g->validate (array ('name' => 'Pass'))); // pass
var_dump ($g->validate (array ('name' => 'Toooooooo Looooooong'))); // fail

// test cleanup

db_execute ('drop table test_generic_one');

?>
--EXPECTF--
int(%d)
stdClass Object
(
    [id] => %d
    [name] => Test 2
)
bool(false)
string(6) "Test 2"
int(2)
int(5)
string(6) "Test 1"
int(5)
int(5)
stdClass Object
(
    [id] => %d
    [name] => Test 1
)
string(%d) "%d"
string(1) "5"
stdClass Object
(
    [name] => Test 1
    [id] => %d
)
string(6) "Test 2"
string(1) "1"
string(1) "0"
bool(false)
bool(true)
bool(false)