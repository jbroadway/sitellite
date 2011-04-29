; <?php /*

[Collection]

name = sitestudy_item
app = sitestudy
display = Case Studies
singular = Case Study

key_field = id
key_field_name = ID

title_field = client
title_field_name = Client

body_field = solution
summary_field = description
keywords_field = keywords

is_versioned = yes
visible = yes
sitesearch_url = "sitestudy-app/case.%s"
sitesearch_access = public

add = "form:sitestudy/add"
edit = "form:sitestudy/edit"

[Source]

name = Database

[Store]

name = Database

[browse:client]

header = Client
width = 20%

[browse:problem]

header = Problem
length = 28
width = 23%

[browse:solution]

header = Solution
length = 28
width = 23%

[browse:sitellite_status]

header = Status
width = "10%"

[browse:sitellite_access]

header = Access
width = "10%"

[facet:client]

display			= Text
type			= text
fields			= "id, client, problem, solution, keywords, description"

[facet:sitellite_status]

display			= Status
type			= select
values			= "session_get_statuses ()"

[facet:sitellite_access]

display			= Access Level
type			= select
values			= "session_get_access_levels ()"

; */ ?>