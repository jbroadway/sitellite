; <?php /*

[Meta]

name		= Upcoming Events
description	= Sidebar list of upcoming events

[category]

alt			= Category
type		= select
setValues	= "eval: array_merge (array ('' => '- SELECT -'), assocify (db_shift_array ('select distinct category from siteevent_event where category != `` order by category asc')))"

[limit]

type		= text
alt			= Limit

[location]

alt         = Show location
type        = select
setValues   = "eval: array (intl_get ('no'), intl_get ('yes'))"

; */ ?>