; <?php /*

[Collection]

name = sitellite_homepage

display = Member Pages
singular = Member Page

key_field = user
key_field_name = User

title_field = user
title_field_name = User

is_versioned = no
translate = no

[Source]

name = Database

[Store]

name = Blank

[browse:user]

header = User
;width = "20%"
width = "25%"

[browse:title]

header = Title
;width = "20%"
width = "25%"

;[browse:template]

;header = Template
;width = "20%"

[browse:body]

header = Body
;width = "32%"
;length = 40
width = "40%"
length = 50
filter = strip_tags

[hint:user]

type = select
setValues = "eval: db_pairs ('select username, concat(role, ` - `, lastname, ` `, firstname, ` (`, username, `)`) as name from sitellite_user order by name asc')"

[hint:body]

type = xed.Widget.Xeditor

[hint:template]

;type = select
;setValues = "eval: array ('default' => 'Default')"
type = hidden
setValue = ""

[hint:title]

extra = "size='40'"

[facet:title]

type = text
fields = "user, title, body"
display = Text

[facet:user]

type = select
display = User
values = "db_shift_array ('select distinct user from sitellite_homepage order by user asc')"

;[facet:template]

;type = select
;display = Template

; */ ?>
