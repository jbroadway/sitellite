; <?php /*

[Meta]

name		= Headlines
description	= Latest news story headlines.

[sec]

type		= select
alt			= Section
setValues	= "eval: loader_call ('news.Story', 'NewsStory::getCategories')"

[limit]

type		= text
alt			= Limit
setDefault	= 5

[archive]

type		= select
alt			= Show archive
setValues	= "eval: array ('yes' => 'Yes', 'no' => 'No')"

[dates]

type		= select
alt			= Include Dates
setValues	= "eval: array ('yes' => 'Yes', 'no' => 'No')"

[thumbs]

type		= select
alt			= Include Thumbnails
setValues	= "eval: array ('yes' => 'Yes', 'no' => 'No')"

; */ ?>