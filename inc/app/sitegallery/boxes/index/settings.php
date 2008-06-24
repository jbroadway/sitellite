; <?php /*

[Meta]

name		= List of Galleries from Image Chooser
description	= View a list of galleries from folders of images in the Image Chooser.

[path]

type		= select
alt			= "Root Folder (sub-folders are galleries)"
setValues	= "eval: loader_call ('cms.Widget.Folder', 'MF_Widget_folder::getList', 'pix')"

; */ ?>