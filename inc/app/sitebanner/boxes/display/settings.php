; <?php /*

[Meta]

name		= Banner Ad
description	= Display a banner ad

[position]

type = select
alt = Screen Position
setValues = "eval: assocify (db_shift_array ('select name from sitebanner_position order by name asc'))"

; */ ?>