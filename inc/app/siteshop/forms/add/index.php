<?php

class SiteshopAddForm extends MailForm {
	function SiteshopAddForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/siteshop/forms/add/settings.php');
		page_title (intl_get ('Add a Product'));
	}

	function onSubmit ($vals) {
		unset ($vals['tab1']);
		unset ($vals['tab2']);
		unset ($vals['tab3']);
		unset ($vals['tab4']);
		unset ($vals['tab-end']);
		unset ($vals['submit_button']);

		$images = array ();
		$images[] = $vals['image1'];
		$images[] = $vals['image2'];
		$images[] = $vals['image3'];
		$images[] = $vals['image4'];
		$images[] = $vals['image5'];
		$images[] = $vals['image6'];
		unset ($vals['image1']);
		unset ($vals['image2']);
		unset ($vals['image3']);
		unset ($vals['image4']);
		unset ($vals['image5']);
		unset ($vals['image6']);

		$categories = array ();
		$categories[] = $vals['category1'];
		$categories[] = $vals['category2'];
		$categories[] = $vals['category3'];
		unset ($vals['category1']);
		unset ($vals['category2']);
		unset ($vals['category3']);

		$p = new Product ($vals);

		foreach ($categories as $cat) {
			if ($cat) {
				$p->setCategory (new Category ($cat));
			}
		}

		foreach ($images as $n => $image) {
			if (is_object ($image) && preg_match ('/\.jpg$/i', $image->name)) {
				$image->move ('inc/app/siteshop/data', $p->val ('id') . '-' . ($n + 1) . '.jpg');
			}
		}

		header ('Location: ' . site_prefix () . '/index/siteshop-admin-products-action');
		exit;
	}
}

?>