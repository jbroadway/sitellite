; <?php /*

[Collection]

name = shoutbox
app = shoutbox
display = Shoutbox
singular = Message

key_field = id
key_field_name = ID

title_field = name
title_field_name = Name

body_field = message

add = no
is_versioned = no

order_by = posted_on
sorting_order = desc

[Source]

name = Database

[Store]

name = Blank

[link:edit_settings]

text = Edit settings
url = "/usradm-applications-edit-action?appname=shoutbox"

[browse:posted_on]

header = Posted on
width = "150"

[browse:name]

header = Name

[browse:message]

header = Message
filter = filter_smiley
filter_import = smiley.Smiley

[browse:ip_address]

header = IP Address
width = "110"

[facet:name]

display = Name
type = text
fields = "name"

[facet:message]

display = Message
type = text
fields = "message"

[facet:ip_address]

display = Ip Address
type = text
fields = "ip_address"

[hint:url]

type = hidden

[hint:message]

type = textarea

[hint:ip_address]

type = hidden

; */ ?>