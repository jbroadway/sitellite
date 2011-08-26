<?php

loader_import ('saf.Database.Generic');

class CustomOption extends Generic {
	function getImage () {
		if (file_exists ('inc/app/siteshop/pix/options/' . $this->val ('id') . '.jpg')) {
			return site_prefix() . '/inc/app/siteshop/pix/options/' . $this->val ('id') . '.jpg';
		}
		else return false;
	}
}

?>
