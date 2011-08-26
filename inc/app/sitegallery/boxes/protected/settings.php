; <?php /*

[Meta]

name		= Protected Image
description	= View an image using DHTML trickery to make saving the image more difficult.

[image]

type		= imagechooser
alt			= Image
path		= /pix

[float]

type		= select
alt			= Alignment
setValues	= "eval: array ('' => '-- ' . intl_get ('None') . ' --', 'left' => intl_get ('Left'), 'right' => intl_get ('Right'))"

[watermark]

type		= text
alt			= "Watermark Text (Optional)"

; */ ?>