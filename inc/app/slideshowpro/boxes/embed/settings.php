[Meta]

name = Embed a Slideshow

[title]

type = text

[description]

type = textarea
cols = 30
rows = 3

[folder]

type = select
setValues	= "eval: loader_call ('cms.Widget.Folder', 'MF_Widget_folder::getList', 'pix')"
