--TEST--
saf.Misc.Ini
--FILE--
<?php

// test setup

include_once ('../init.php');

// include library

loader_import ('saf.Misc.Ini');

// constructor method

$ini = new Ini;

// parse() and parseStr() methods

define ('TEST', 'Test Value');
$out = $ini->parse ('one = test
two = "test"
three = 1
four = 0
five = 2
six = On
seven = Off
eight = TEST
nine = yes
ten = no
eleven = true
twelve = false
thirteen = "true"
fourteen = "false"
', false);
var_dump ($out);

// filter() method

$ini->addFilter ('ini_filter_split_commas', array ('comma_separated'));
var_dump ($ini->filter (array ('comma_separated' => 'one, two, three'), false, false));
var_dump ($ini->filter (array ('comma_separated' => array ('one', 'two', 'three')), false, true));

// write() and writeValue() methods

var_dump ($ini->write ($out, false));

// fromXml() and toXml() methods

$xml = $ini->toXml ($out);

var_dump ($xml);

var_dump ($ini->fromXml ($xml));

?>
--EXPECT--
array(14) {
  ["one"]=>
  string(4) "test"
  ["two"]=>
  string(4) "test"
  ["three"]=>
  string(1) "1"
  ["four"]=>
  string(1) "0"
  ["five"]=>
  string(1) "2"
  ["six"]=>
  string(1) "1"
  ["seven"]=>
  string(0) ""
  ["eight"]=>
  string(10) "Test Value"
  ["nine"]=>
  string(1) "1"
  ["ten"]=>
  string(0) ""
  ["eleven"]=>
  string(1) "1"
  ["twelve"]=>
  string(0) ""
  ["thirteen"]=>
  string(4) "true"
  ["fourteen"]=>
  string(5) "false"
}
array(1) {
  ["comma_separated"]=>
  array(3) {
    [0]=>
    string(3) "one"
    [1]=>
    string(3) "two"
    [2]=>
    string(5) "three"
  }
}
array(1) {
  ["comma_separated"]=>
  string(15) "one, two, three"
}
string(924) "; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS
;
; WARNING: This file was automatically generated, and it may
; not be wise to edit it by hand.  If there is an interface
; to modify files of this type, please use that interface
; instead of manually editing this file.  If you are not sure
; or are not aware of such an interface, please talk to your
; Sitellite administrator first.
;

one                     = test

two                     = test

three                   = On

four                    = Off

five                    = 2

six                     = On

seven                   = Off

eight                   = Test Value

nine                    = On

ten                     = Off

eleven                  = On

twelve                  = Off

thirteen                = true

fourteen                = false

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>"
string(278) "<INI>
	<one>test</one>
	<two>test</two>
	<three>1</three>
	<four>0</four>
	<five>2</five>
	<six>1</six>
	<seven></seven>
	<eight>Test Value</eight>
	<nine>1</nine>
	<ten></ten>
	<eleven>1</eleven>
	<twelve></twelve>
	<thirteen>true</thirteen>
	<fourteen>false</fourteen>
</INI>
"
array(14) {
  ["one"]=>
  string(4) "test"
  ["two"]=>
  string(4) "test"
  ["three"]=>
  string(1) "1"
  ["four"]=>
  string(1) "0"
  ["five"]=>
  string(1) "2"
  ["six"]=>
  string(1) "1"
  ["seven"]=>
  string(0) ""
  ["eight"]=>
  string(10) "Test Value"
  ["nine"]=>
  string(1) "1"
  ["ten"]=>
  string(0) ""
  ["eleven"]=>
  string(1) "1"
  ["twelve"]=>
  string(0) ""
  ["thirteen"]=>
  string(4) "true"
  ["fourteen"]=>
  string(5) "false"
}