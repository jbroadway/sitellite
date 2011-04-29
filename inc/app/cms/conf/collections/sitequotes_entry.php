; <?php /*

[Collection]

name = sitequotes_entry
app = sitequotes
display = Quotes
singular = Quote

key_field = id
key_field_name = ID

title_field = person
title_field_name = Person

body_field = quote

is_versioned = no

[Source]

name = Database

[Store]

name = Blank

[browse:id]

header = ID
width = "7%"
align = center

[browse:person]

header = Person
width = "18%"

[browse:company]

header = Company
width = "18%"

[browse:quote]

header = Quote
width = "50%"
length = 70

[hint:quote]

type = xed.Widget.Xeditor

[facet:person]

type = text
display = Text
fields = "id, person, company, website, quote"

; */ ?>