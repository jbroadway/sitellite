; <?php /*

[Meta]

name		= Slideshow with effect from Image Chooser
description	= View an Image Chooser folder as a slideshow of images.

[title]

type		= text
alt			= Gallery Title

[path]

type		= select
alt			= Folder
setValues	= "eval: loader_call ('cms.Widget.Folder', 'MF_Widget_folder::getList', 'pix')"

[effect]

type		= select
alt			= Effect Type
setValues	= "eval: array ('fade' => 'Fade in/out', 'cover' => 'Cover', 'toss' => 'Toss', 'zoom' => 'Zoom')"



; */ ?>