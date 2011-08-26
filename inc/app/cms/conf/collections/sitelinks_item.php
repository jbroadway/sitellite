; <?php /*

[Collection]

name			= sitelinks_item
app				= sitelinks
display			= Links
singular		= Link

; This determines whether versioning is enabled or not for this collection.
is_versioned	= yes
sitesearch_url	= "sitelinks-app/item.%s"
sitesearch_access	= public
edit_extras		= "sitelinks/properties"

; This determines the display order of the collections in the control panel.
; The higher the number, the closer to the top of the list.  We make the
; sitellite_page number very high, so that it is always on top (logically so),
; but an ordinary default should be between 1 and 10.  Values of 0 will simply
; not be displayed in the list (which is desirable in some circumstances).
;list_weight		= 3

key_field		= id

title_field		= title
title_field_name= Title

summary_field	= summary
keywords_field	= category
body_field		= summary

; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.
;add				= sitelinks/add
;edit			= sitelinks/edit

; Additional fields to display

[Source]

name			= Database

[Store]

name			= Database

[browse:title]

header			= Link Name

[browse:category]

header			= Category

[browse:sitellite_status]

header			= Status
width			= "10%"

[browse:sitellite_access]

header			= Access
width			= "10%"

[hint:url]

alt				= Link URL
extra			= "size=`40`"

[hint:user_id]

alt				= Owner

[hint:rank]

extra			= "size=`5`"
default_value	= "0"

[hint:user_rating]

type			= hidden

[hint:category]

type			= selector
table			= sitelinks_category

[hint:ctype]

type			= sitelinks.Widget.Linktype
alt				= Type

[hint:ts]

type			= calendar
alt				= "Last Modified"
nullable		= yes
showsTime		= yes
displayFormat		= "datetime"
setValue		= DATETIME

[hint:summary]

labelPosition	= left

[hint:sitellite_status]

type			= status
alt				= Status

[hint:sitellite_access]

type			= access
alt				= Access Level

[hint:sitellite_owner]

type = owner
alt = Created By

[hint:sitellite_team]

type = team
alt = Owned by Team

[facet:category]

display			= Category
type			= select
values			= "db_shift_array ('select id from sitelinks_category order by id asc')"

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

; */ ?>