; <?php /*

[Collection]

name			= sitellite_news
app				= news
display			= News Stories
singular		= Story
icon			= inc/app/cms/pix/icons/sitellite_news.gif

; This determines whether versioning is enabled or not for this collection.
is_versioned	= yes
sitesearch_url		= "news-app/story.%s"
sitesearch_access	= public

; This determines the display order of the collections in the control panel.
; The higher the number, the closer to the top of the list.  We make the
; sitellite_page number very high, so that it is always on top (logically so),
; but an ordinary default should be between 1 and 10.  Values of 0 will simply
; not be displayed in the list (which is desirable in some circumstances).
list_weight		= 3

key_field		= id
key_field_name	= ID
key_field_align	= right

title_field		= title
title_field_name= Title

summary_field	= summary
keywords_field	= author,category
body_field		= body

; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.
add				= news/add
edit			= news/edit

; Additional fields to display

[Source]

name			= Database

[Store]

name			= Database

[browse:title]

header			= Title

[browse:category]

header			= Section

[browse:date]

header			= Date
filter			= cms_filter_date
filter_import	= cms.Filters
width			= "15%"

[browse:sitellite_status]

header			= Status
width			= "10%"

[browse:sitellite_access]

header			= Access
width			= "10%"

[facet:category]

display			= Section
type			= select
values			= "db_shift_array ('select name from sitellite_news_category order by name asc')"

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

[facet:body]

display			= Body
type			= text
fields			= "body"

[hint:rank]

type = hidden

[hint:author]

type = hidden

[hint:category]

type = hidden

[hint:summary]

rows = 3
labelPosition = left

[hint:body]

type = xed.Widget.Xeditor

[hint:thumb]

type = hidden

[hint:external]

type = hidden

; */ ?>
