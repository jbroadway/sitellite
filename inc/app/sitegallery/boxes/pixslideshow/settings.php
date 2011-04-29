; <?php /*

[Meta]

name		= Slideshow from Image Chooser
description	= View an Image Chooser folder as a slideshow of images.

[title]

type		= text
alt			= Gallery Title

[path]

type		= select
alt			= Folder
setValues	= "eval: loader_call ('cms.Widget.Folder', 'MF_Widget_folder::getList', 'pix')"

[delay]

type		= select
alt			= Delay Between Photos
setValues	= "eval: array (6 => '5 Seconds', 12 => '10 Seconds', 1 => 'Manual')"

[descriptions]

type		= select
alt			= Show Descriptions
setValues	= "eval: assocify (array ('yes', 'no'))"

; */ ?>