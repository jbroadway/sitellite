; <?php /*

[Collection]

name			= sitemailer2_message
display			= Mailing Lists
singular		= Mailing Lists
is_versioned		= no
key_field		= id
key_field_name		= ID
title_field		= title
title_field_name	= Title
visible			= no

; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.

add			    = form:sitemailer2/msg
edit			= form:sitemailer2/msg

; Additional fields to display

[Source]

name			= Database

[Store]

name			= Database

[browse:title]

header			= MailMessageTitle
width			= "40%"

[browse:date]

header			= Created
width			= "15%"

[browse:template]

header			= Template Used
width			= "20%"
filter_import = sitemailer2.Filters
filter = sitemailer2_filter_template

[browse:status]

header			= Status
width			= "10%"


[facet:id]

display			= ID
type			= text
fields			= "id"
equal			= on

[facet:title]

display			= Title
type			= text
fields			= "title"

[facet:template]

display			= Template Used
type			= select
values			= "db_pairs ('select id,title from sitemailer2_template order by title asc')"

[hint:date]

type			= select
alt			= Year
setValues		= "eval: assocify (range (date ('Y') + 1, date ('Y') - 25))"
setDefault		= "eval: date ('Y')"

; */ ?>
