<?php

require_once 'XML/Tree.php';

$tree = new XML_Tree;

$root =& $tree->addRoot('_#:A');

/* Check for error on the Root node */

if (PEAR::isError($root->error)) {
    var_dump($root->error);
}

$child =& $root->addChild('abc:def', 'bar');

if (PEAR::isError($child)) {
    var_dump($child);
}

$root->registerName('abc','http://some_name_space');

$child->registerName('abc', 'http://some_other_name_space');


echo $tree->getNodeNamespace($child);

if (PEAR::isError($child)) {
    var_dump($child);
}

echo $tree->get();

?>