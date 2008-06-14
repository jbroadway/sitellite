<?php

if (conf ('Product', 'name')) {
	define ('PRODUCT_NAME', conf ('Product', 'name'));
} else {
	define ('PRODUCT_NAME', 'Sitellite Content Manager ' . SITELLITE_VERSION);
}

if (conf ('Product', 'shortname')) {
	define ('PRODUCT_SHORTNAME', conf ('Product', 'shortname'));
} else {
	define ('PRODUCT_SHORTNAME', 'Sitellite');
}

if (conf ('Product', 'copyright')) {
	define ('PRODUCT_COPYRIGHT', conf ('Product', 'copyright'));
} else {
	define ('PRODUCT_COPYRIGHT', 'Simian Systems Inc.');
}

if (conf ('Product', 'copyright_website')) {
	define ('PRODUCT_COPYRIGHT_WEBSITE', conf ('Product', 'copyright_website'));
} else {
	define ('PRODUCT_COPYRIGHT_WEBSITE', 'http://www.simian.ca/');
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

define ('PRODUCT_EDITION', 'Open Source Edition');

?>