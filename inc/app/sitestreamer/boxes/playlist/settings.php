; <?php /*

[Meta]

name		= Playlist
description	= Generate a playlist of audio files

[title]

type		= text
alt			= Playlist Link Text
setDefault	= M3U Playlist

[path]

type		= select
alt			= Folder
setValues	= "eval: loader_call ('cms.Widget.Folder', 'MF_Widget_folder::getList', 'inc/data')"

[limit]

type		= select
alt			= Limit
setValues	= "eval: array (0 => 'None', 10 => '10 Files', 20 => '20 Files')"

; */ ?>