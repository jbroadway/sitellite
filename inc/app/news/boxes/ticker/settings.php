; <?php /*

[Meta]

name = News Ticker
description	= Latest news stories scrolling horizontally.

[section]

type		= select
alt			= Section
setValues	= "eval: loader_call ('news.Story', 'NewsStory::getCategories')"

[bg]

type		= text
alt			= "Background Colour (hex)"
setDefault	= f2f2f2

[width]

type		= text
alt			= Width
setDefault	= 450px

[border]

type		= select
alt			= Border
setValues	= "eval: array ('yes' => intl_get ('Yes'), 'no' => intl_get ('No'))"

; */ ?>