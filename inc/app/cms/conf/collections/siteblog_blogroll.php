; <?php /*

[Collection]

name = siteblog_blogroll
display = Blog Roll
singular = Link
key_field = id
title_field = title
is_versioned = no
translate = no

[Source]

name = Database

[Store]

name = Blank

[browse:title]

header = Link Name

[browse:url]

header = Link URL
filter_import = "siteblog.Filters"
filter = "siteblog_filter_link"

[browse:weight]

header = Sorting Weight
align = right
width = "15%"

[hint:title]

alt = Link Name

[hint:url]

alt = Website URL

[hint:weight]

alt = Sorting Weight
default_value = "0"

; */ ?>