<?php

loader_import ('saf.Database.Generic');

class CustomPromo extends Generic {
	function code ($code) {
		$p = new Promo ();
		$list = $p->find (array ('code' => $code, 'expires > ' . date ('Y-m-d')));
		if (! $list) {
			return false;
		}
		return array_shift ($list);
	}
}

?>