; <?php /*

[Collection]

name			= sitellite_page
display			= Web Pages
singular		= Page
icon			= inc/app/cms/pix/icons/sitellite_page.gif

; This determines the display order of the collections in the control panel.
; The higher the number, the closer to the top of the list.  We make the
; sitellite_page number very high, so that it is always on top (logically so),
; but an ordinary default should be between 1 and 10.  Values of 0 will simply
; not be displayed in the list (which is desirable in some circumstances).
list_weight		= 100

key_field		= id
key_field_name	= ID

title_field		= title
title_field_name= Page Title

summary_field	= description
keywords_field	= keywords
body_field		= body

; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.
add				= form:cms/add/sitellite_page
edit			= form:cms/edit/sitellite_page

is_versioned	= yes
sitesearch_url		= "%s"
sitesearch_access	= public
sitesearch_include_field = include_in_search

[Source]

name			= Database

[Store]

name			= Database

[browse:id]

header			= ID
width			= "15%"

[browse:title]

header			= Page Title

[browse:below_page]

header			= Parent
width			= "15%"

[browse:sitellite_status]

header			= Status
width			= "10%"

[browse:sitellite_access]

header			= Access
width			= "10%"

[facet:title]

display			= Text
type			= text
fields			= "id, title, nav_title, head_title, description, keywords, body"

[facet:template]

display			= Template
type			= select
values			= "db_shift_array ('select distinct template from sitellite_page where template != `` order by template asc')"

[facet:include]

display			= Included in Navigation
type			= select
values			= "array ('yes', 'no')"

[facet:keywords]

display			= Keyword
type			= select
values			= "db_shift_array ('select distinct word from sitellite_keyword order by word asc')"
fuzzy			= yes

[facet:sitellite_owner]

display			= Created By
type			= select
values			= "db_shift_array ('select distinct sitellite_owner from sitellite_page where sitellite_owner != `` order by sitellite_owner asc')"

[facet:sitellite_team]

display			= Owned by Team
type			= select
values			= "db_shift_array ('select distinct sitellite_team from sitellite_page where sitellite_team != `` order by sitellite_team asc')"

[facet:sitellite_status]

display			= Status
type			= select
values			= "session_get_statuses ()"

[facet:sitellite_access]

display			= Access Level
type			= select
values			= "session_get_access_levels ()"

[facet:is_section]

display			= Section Index
type			= select
values			= "array ('yes', 'no')"

[hint:id]

alt = Page ID

[hint:title]

alt = Page Title

[hint:nav_title]

alt = Navigation Title

[hint:head_title]

alt = Window Title

[hint:below_page]

type = hidden

[hint:template]

type = hidden

[hint:external]

type = hidden

[hint:sort_weight]

type = hidden

[hint:keywords]

type = hidden

[hint:description]

rows = 3
labelPosition = left

[hint:body]

type = xed.Widget.Xeditor

; */ ?>