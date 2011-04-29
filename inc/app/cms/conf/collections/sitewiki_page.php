; <?php /*

[Collection]

name = sitewiki_page
app = sitewiki
display = Wiki Pages
singular = Wiki Page
key_field = id
title_field = id
is_versioned	= yes
visible = no
allow_uppercase_key = yes

[Source]

name = Database

[Store]

name = Database

[browse:id]

header = Page
width = 15%

[browse:owner]

header = Owner
width = 15%

[browse:updated_on]

header = Last Modified
width = 25%
filter = cms_filter_date_time
filter_import = cms.Filters

[browse:view_level]

header = Visible To
width = 18%
filter = sitewiki_filter_level
filter_import = sitewiki.Filters

[browse:edit_level]

header = Editable By
width = 18%
filter = sitewiki_filter_level

[facet:id]

type = text
display = Text
fields = "id, body"

[facet:owner]

type = select
display = Owner
values = "db_shift_array (`select distinct owner from sitewiki_page where owner != ''`)"

; */ ?>
