; <?php /*

[Collection]

name			= sitepresenter_presentation
app				= sitepresenter
display			= Presentations
singular		= Presentation

; This determines whether versioning is enabled or not for this collection.
is_versioned	= no
sitesearch_url	= "sitepresenter-presentation-action/id.%s"
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

summary_field	= cover
;keywords_field	= category
body_field		= cover

; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.
add				= sitepresenter/add
edit			= sitepresenter/edit

; Additional fields to display

[Source]

name			= Database

[Store]

name			= Blank

[browse:run]

header			= ""
filter_import	= sitepresenter.Filters
virtual			= sitepresenter_virtual_run
width			= "8%"
align			= center

[browse:title]

header			= Title

[browse:category]

header			= Category
width			= "15%"

[browse:slides]

header			= Slides
filter_import	= sitepresenter.Filters
virtual			= sitepresenter_virtual_slides
width			= "8%"
align			= center

[browse:views]

header			= Views
filter_import	= sitepresenter.Filters
virtual			= sitepresenter_virtual_views
width			= "8%"
align			= center

[browse:sitellite_status]

header			= Status
width			= "10%"

[browse:sitellite_access]

header			= Access
width			= "10%"

[facet:category]

display			= Category
type			= select
values			= "db_shift_array ('select distinct category from sitepresenter_presentation where category != `` order by category asc')"

[facet:sitellite_status]

display			= Status
type			= select
values			= "session_get_statuses ()"

[facet:sitellite_access]

display			= Access Level
type			= select
values			= "session_get_access_levels ()"

; */ ?>