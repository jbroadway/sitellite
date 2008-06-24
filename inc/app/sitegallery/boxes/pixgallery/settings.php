; <?php /*

[Meta]

name		= Gallery from Image Chooser
description	= View an Image Chooser folder as a gallery of images.

[title]

type		= text
alt			= Gallery Title

[path]

type		= select
alt			= Folder
setValues	= "eval: loader_call ('cms.Widget.Folder', 'MF_Widget_folder::getList', 'pix')"

[descriptions]

type		= select
alt			= Show Descriptions
setValues	= "eval: assocify (array ('yes', 'no'))"

; */ ?>