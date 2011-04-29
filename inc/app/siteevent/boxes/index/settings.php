; <?php /*

[Meta]

name		= Full Calendar
description	= Display the full SiteEvent calendar

[category]

alt			= Category
type		= select
setValues	= "eval: array_merge (array ('' => '- SELECT -'), assocify (db_shift_array ('select distinct category from siteevent_event where category != `` order by category asc')))"

; */ ?>