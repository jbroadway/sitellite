; <?php /*

[Collection]

name			        = siteblog_post
app					= siteblog
display			    = Blog Posts
singular		        = Blog Post
is_versioned		    = yes
key_field		    = id
key_field_name		= ID
title_field         = subject 


; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.
; Additional fields to display

add  = form:siteblog/edit
edit = form:siteblog/edit

sitesearch_url = "siteblog-view-action/id.%s"
sitesearch_access = public

[Source]

name		      	= Database

[Store]

name			    = Database

[browse:subject]

header			= Title
width = "36%"

[browse:author]

header			= Author
width			= "10%"

[browse:category]

header = Category
width = "12%"
filter_import = "siteblog.Filters"
filter = "siteblog_filter_category"

[browse:status]

header			= Status
width			= "10%"
filter_import = "siteblog.Filters"
filter = "siteblog_filter_status"

[browse:created]

header			= Created
width			= "20%"
filter_import = "siteblog.Filters"
filter = "siteblog_filter_nicedate"

;[facet:id]

;display			= ID
;type			    = text
;fields			= "id"
;equal			= on

[facet:subject]

display			= Text
type			    = text
fields			= "subject, body"

[facet:author]

display = Author
type = select
values = "db_shift_array ('select distinct author from siteblog_post order by author asc')"

[facet:Category]

display = Category
type = select
values = "db_pairs ('select id, title from siteblog_category order by title asc')"

[facet:status]

display = Status
type = select
values = "array ('visible' => 'Published', 'not visible' => 'Draft')"

[hint:category]

type = hidden

[hint:author]

type = hidden

[hint:body]

type = xed.Widget.Xeditor

; */ ?>