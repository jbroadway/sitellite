; <?php /*

[Collection]

name = petition
app = petition
display = Petitions
singular = Petition

is_versioned = no
sitesearch_url = "petition-app/id.%s"
sitesearch_access = public

key_field = id
title_field = name
title_field_name = Title

summary_field = description
body_field = body

;add = petition/add
;edit = petition/edit

[Source]

name = Database

[Store]

name = Blank

[browse:name]

header = Title

[browse:signatures]

header = Signatures
filter_import = petition.Filters
virtual = petition_filter_signatures
align = center
width = "10%"

[browse:ts]

header = Date/Time
filter_import	= cms.Filters
filter			= cms_filter_date_time
align = center
width = "20%"

[browse:sitellite_status]

header = Status
filter = ucwords
align = center
width = "10%"

[browse:sitellite_access]

header = Access
filter = ucwords
align = center
width = "10%"

[facet:name]

display = Text
type = text
fields = name, description, body

[facet:sitellite_status]

display			= Status
type			= select
values			= "session_get_statuses ()"

[facet:sitellite_access]

display			= Access Level
type			= select
values			= "session_get_access_levels ()"

[hint:name]

alt = Title
extra = "size=`40`"

[hint:ts]

type = calendar
alt = "Date/Time"
nullable = no
showsTime = yes
displayFormat	= "datetime"
setValue = DATETIME

[hint:description]

labelPosition = top
cols = 80
rows = 4

[hint:body]

type = xed.Widget.Xeditor

[hint:sitellite_status]

type = status
alt = Status
defalut_value = draft

[hint:sitellite_access]

type = access
alt = Access Level
default_value = public

[hint:sitellite_owner]

type = owner
alt = Created By

[hint:sitellite_team]

type = team
alt = Owned by Team

; */ ?>