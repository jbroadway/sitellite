[Meta]

name = FAQ Category
description = Display a single category of FAQ questions

[category]

alt = Category
type = select
setValues = "eval: assocify (db_shift_array ('select name from sitefaq_category order by name asc'))"
