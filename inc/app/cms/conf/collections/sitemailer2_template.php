; <?php /*

[Collection]

name			= sitemailer2_template
display			= Mailing List Templates
singular		= Mailing List Templates
is_versioned		= no
key_field		= id
key_field_name		= ID
title_field		= title
title_field_name	= Title
visible                 = no

; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.

add				= form:sitemailer2/template
edit			= form:sitemailer2/template

; Additional fields to display

[Source]

name			= Database

[Store]

name			= Database

[browse:title]

header			= Template Title
width			="35%"

[browse:subject]

header			= Subject
width			="35%"

[browse:date]

header			= Created
width			= "15%"

[facet:id]

display			= ID
type			= text
fields			= "id"
equal			= on

[facet:title]

display			= Title
type			= text
fields			= "title"

[facet:subject]

display			= Subject
type			= text
fields			= "subject"

[hint:date]

type			= select
alt			= Year
setValues		= "eval: assocify (range (date ('Y') + 1, date ('Y') - 25))"
setDefault		= "eval: date ('Y')"

; */ ?>
