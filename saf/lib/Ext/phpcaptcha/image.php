<?php
// This file is used to display the CAPTCHA image and contains the default settings

// YOU CANNOT USE sitellite code in this file, as it is accessed directly

$dr = $_SERVER["DOCUMENT_ROOT"];

include_once $dr . "/saf/lib/Ext/phpcaptcha/common.php";
include_once $dr . "/saf/lib/Ext/phpcaptcha/class.captcha.php";

$captcha = new Captcha();

// Number of characters
$captcha->chars_number = 6;

// Letters (1), Numbers (2), Letters & Numbers (3)
$captcha->string_type = 3;

// Font size
$captcha->font_size = 14;

// Font
$captcha->tt_font = 'verdana.ttf';

// Opacity (0 = opaque, 127 = transparent)
$captcha->opacity = 0;

// Font color; format 'r,g,b'
$captcha->font_color = '0,0,0';

// Border color; format 'r,g,b'
// $captcha->border_color = '191, 120, 120';

// Show the image, (width,height)
$captcha->show_image(102, 30);
?>