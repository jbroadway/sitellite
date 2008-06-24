[Meta]

name = Glossary Category
description = Display a single category of glossary entries

[category]

alt = Category
type = select
setValues = "eval: assocify (db_shift_array ('select name from siteglossary_category order by name asc'))"
