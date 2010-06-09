<?php

$c = new Category ();
$c->orderBy ('weight desc, name asc');
$list = $c->find (array ());

echo template_simple ('categories.spt', $list);

?>