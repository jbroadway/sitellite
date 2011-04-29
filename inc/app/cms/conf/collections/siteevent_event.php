; <?php /*

[Collection]

name			= siteevent_event
app				= siteevent
display			= Events
singular		= Event

; This determines whether versioning is enabled or not for this collection.
is_versioned	= yes
sitesearch_url	= "siteevent-details-action/id.%s"
sitesearch_access	= public

; This determines the display order of the collections in the control panel.
; The higher the number, the closer to the top of the list.  We make the
; sitellite_page number very high, so that it is always on top (logically so),
; but an ordinary default should be between 1 and 10.  Values of 0 will simply
; not be displayed in the list (which is desirable in some circumstances).
;list_weight		= 3

key_field		= id

title_field		= title
title_field_name= Title

summary_field	= details
keywords_field	= category
body_field		= details

; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.
add				= siteevent/add
edit			= siteevent/edit

order_by = date
sorting_order = desc

; Additional fields to display

[Source]

name			= Database

[Store]

name			= Database

[browse:title]

header			= Event Name

[browse:date]

header			= Date
filter_import	= siteevent.Filters
virtual			= siteevent_virtual_date
width			= "15%"

[browse:recurring]

header			= Repeats
filter_import	= siteevent.Filters
virtual			= siteevent_virtual_recurring
width			= "6%"

[browse:category]

header			= Category

[browse:audience]

header			= Audience
filter			= siteevent_filter_audience
filter_import	= siteevent.Filters

[browse:sitellite_status]

header			= Status
width			= "7%"
filter			= ucwords

[browse:sitellite_access]

header			= Access
width			= "7%"
filter			= ucwords

[facet:category]

display			= Category
type			= select
values			= "db_shift_array ('select name from siteevent_category order by name asc')"

;[facet:audience]

;display			= Audience
;type			= select
;values			= "db_shift_array ('select name from siteevent_audience order by name asc')"

[facet:sitellite_status]

display			= Status
type			= select
values			= "session_get_statuses ()"

[facet:sitellite_access]

display			= Access Level
type			= select
values			= "session_get_access_levels ()"

[facet:id]

display			= ID
type			= text
fields			= "id"
equal			= on

[facet:title]

display			= Title
type			= text
fields			= "title"

[facet:summary]

display			= Body
type			= text
fields			= "summary"

[hint:category]

type = hidden

[hint:audience]

type = hidden

[hint:loc_name]

type = hidden

[hint:loc_address]

type = hidden

[hint:loc_city]

type = hidden

[hint:loc_province]

type = hidden

[hint:loc_country]

type = hidden

[hint:loc_map]

type = hidden

[hint:contact]

type = hidden

[hint:contact_email]

type = hidden

[hint:contact_phone]

type = hidden

[hint:contact_url]

type = hidden

[hint:details]

type = xed.Widget.Xeditor

; */ ?>