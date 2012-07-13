<?php

if (conf ('Product', 'name')) {
	define ('PRODUCT_NAME', conf ('Product', 'name'));
} else {
	define ('PRODUCT_NAME', 'Sitellite Content Management System ' . SITELLITE_VERSION);
}

if (conf ('Product', 'shortname')) {
	define ('PRODUCT_SHORTNAME', conf ('Product', 'shortname'));
} else {
	define ('PRODUCT_SHORTNAME', 'Sitellite');
}

if (conf ('Product', 'dealer')) {
	define ('PRODUCT_DEALER', conf ('Product', 'dealer'));
} else {
	define ('PRODUCT_DEALER', 'Dealer');
}

if (conf ('Product', 'dealer_website')) {
	define ('PRODUCT_DEALER_WEBSITE', conf ('Product', 'dealer_website'));
} else {
	define ('PRODUCT_DEALER_WEBSITE', 'http://yourwebsite.com/');
}

if (conf ('Product', 'copyright')) {
	define ('PRODUCT_COPYRIGHT', conf ('Product', 'copyright'));
} else {
	define ('PRODUCT_COPYRIGHT', 'Sitellite.org Community');
}

if (conf ('Product', 'copyright_website')) {
	define ('PRODUCT_COPYRIGHT_WEBSITE', conf ('Product', 'copyright_website'));
} else {
	define ('PRODUCT_COPYRIGHT_WEBSITE', 'http://www.sitellite.org/');
}

if (conf ('Product', 'license')) {
	define ('PRODUCT_LICENSE', conf ('Product', 'license'));
} else {
	define ('PRODUCT_LICENSE', 'http://www.sitellite.org/index/license');
}

if (conf ('Product', 'header_graphic')) {
	define ('PRODUCT_HEADER_GRAPHIC', conf ('Product', 'header_graphic'));
} else {
	define ('PRODUCT_HEADER_GRAPHIC', site_prefix () . '/inc/app/cms/pix/sitellite-cms.gif');
}

define ('PRODUCT_EDITION', '');

?>