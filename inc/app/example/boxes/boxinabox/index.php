<?php

/**
 * This box is simply going to output some of the other example boxes
 * via the loader_box() function.
 */

// call the example/helloworld box
echo loader_box ('example/helloworld');

// call the example/contact form
echo loader_form ('example/contact');

// set the page title
page_title (intl_get ('Box in a Box'));

?>