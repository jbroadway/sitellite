<?php

require_once 'XML/Tree.php';

$tree = new XML_Tree;

$root =& $tree->addRoot('root');

//$tree->useCdataSections();

$root->addChild('foo','bar');

$baz =& $root->addChild('baz');

$baz->addChild('bat','qux', array(), null, true);
$baz->addChild('bat','quux', array(), null);
$baz->addChild(null, 'foo', array(), null, true);

$tree->dump();

?>