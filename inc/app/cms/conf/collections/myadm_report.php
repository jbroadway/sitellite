; <?php /*

[Collection]

name = myadm_report
app = myadm
display = DB Reports
singular = Report
key_field = id
title_field = name
is_versioned = yes
order_by = name
sorting_order = asc
scheduler_skip = yes

[Source]

name = Database

[Store]

name = Database

[link:myadm]

text = DB Manager
url = /myadm-app
requires resource = app_myadm
requires = rw

[facet:sitellite_status]

type = select
display = Status
values = "session_get_statuses ()"

[facet:sitellite_access]

type = select
display = Access Level
values = "session_get_access_levels ()"

[facet:sitellite_team]

type = select
display = Team
values = "session_get_teams ()"

[browse:run]

header = ""
filter_import = myadm.Filters
virtual = myadm_report_run
width = "5%"
align = center

[browse:name]

header = Report Name
width = "30%"

[browse:created]

header = Created On
filter_import = cms.Filters
filter = cms_filter_date_time
width = "18%"

[browse:sitellite_status]

header = Status
filter = ucfirst
width = "12%"

[browse:sitellite_access]

header = Access Level
filter = ucfirst
width = "12%"

[browse:sitellite_team]

header = Owned By Team
filter = ucfirst
width = "12%"

[hint:name]

extra = "size=`40`"

[hint:created]

type = info
setDefault = "eval: date ('Y-m-d H:i:s')"

[hint:sql_query]

type = textarea
alt = "Report SQL (multiple queries are okay)"
rows = 12
cols = 70

[hint:sitellite_status]

type = status
alt = Status

[hint:sitellite_access]

type = access
alt = Access Level
setDefault = private

[hint:sitellite_startdate]

type = hidden

[hint:sitellite_expirydate]

type = hidden

[hint:sitellite_owner]

type = owner

[hint:sitellite_team]

type = team

; */ ?>