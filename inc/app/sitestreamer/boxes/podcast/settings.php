; <?php /*

[Meta]

name		= Podcast
description	= Generate a podcast RSS feed of audio files

[title]

type		= text
alt			= Podcast Name
setDefault	= Podcast

[path]

type		= select
alt			= Folder
setValues	= "eval: loader_call ('cms.Widget.Folder', 'MF_Widget_folder::getList', 'inc/data')"

[descriptions]

type		= select
alt			= Include Descriptions in RSS
setValues	= "eval: assocify (array ('yes', 'no'))"

[limit]

type		= select
alt			= Limit
setValues	= "eval: array (0 => 'None', 10 => '10 Files', 20 => '20 Files')"

; */ ?>