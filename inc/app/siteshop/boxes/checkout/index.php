<?php

$handler = appconf ('checkout_handler');
echo loader_box ('siteshop/checkout/' . $handler, $parameters, $context);

?>