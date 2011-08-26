; <?php /*

[Collection]
name = digger_linkstory
app = digger
display = Digger Stories
singular = Digger Story
key_field = id
key_field_name = ID
title_field = title
title_field_name = Title
is_versioned = no

[Source]
name = Database

[Store]
name = Blank

[browse:title]
header = Title
width = 33%
length = 50

[browse:score]
header = Score
width = 8%
align = center

[browse:category]
header = Category
width = 12%
align = center
filter = digger_filter_category_name
filter_import = digger.Filters

[browse:user]
header = Posted By
width = 10%
align = center

[browse:posted_on]
header = Posted On
width = 20%
align = center
filter = digger_filter_date
filter_import = digger.Filters

[browse:status]
header = Status
width = 10%
align = center

[hint:title]
extra = "size='30'"

[hint:link]
extra = "size='40'"

[hint:user]
type = select
setValues = "eval: db_pairs ('select username, concat(role, ` - `, lastname, ` `, firstname, ` (`, username, `)`) as name from sitellite_user order by name asc')"

[hint:category]
type = selector
table = digger_category
key = id
title = category

[hint:description]
labelPosition = left

[hint:posted_on]
type = calendar
showsTime = yes
displayFormat	= "datetime"
setDefault = "eval: date('Y-m-d H:i:s')"

[facet:title]
display = Text
type = text
fields = "id, title, description"

[facet:category]
display = Category
type = select
values = "db_pairs ('select * from digger_category order by category asc')"

[facet:user]
display = Posted By
type = select
values = "db_shift_array ('select distinct user from digger_linkstory order by user asc')"

[facet:status]
display = Status
type = select
values = "array ('enabled', 'disabled')"

; */ ?>