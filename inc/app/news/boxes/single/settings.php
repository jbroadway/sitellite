; <?php /*

[Meta]

name		= Single Story Lead-In
description	= Headline/summary of a single news story.

[story]

type		= select
alt			= Story
setValues	= "eval: loader_call ('news.Story', 'NewsStory::allHeadlines')"

[date]

type		= select
alt			= Include Date
setValues	= "eval: array ('yes' => 'Yes', 'no' => 'No')"

[thumb]

type		= select
alt			= Include Thumbnail
setValues	= "eval: array ('yes' => 'Yes', 'no' => 'No')"

[summary]

type		= select
alt			= Include Summary
setValues	= "eval: array ('yes' => 'Yes', 'no' => 'No')"

; */ ?>