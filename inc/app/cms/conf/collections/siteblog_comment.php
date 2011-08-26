; <?php /*

[Collection]

name			        = siteblog_comment
app					= siteblog
display			    = Blog Comments
singular		        = Blog Comment

key_field		    = id
key_field_name		= ID
title_field         = author
is_versioned = no
translate = no

order_by = date
sorting_order = desc


; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.
; Additional fields to display

add  = no
;edit = form:siteblog/comment

[Source]

name		      	= Database

[Store]

name			    = Blank

[link:akismet]

url = /index/siteblog-akismet-action
text = Comment Spam

[facet:author]

type = text
display = Text
fields = author, email, url, body

[facet:child_of_post]

type = select
display = Blog Post
values = "db_pairs ('select id, subject from siteblog_post order by subject asc')"

[browse:author]

header			= Name
width			= "10%"
length = 18

[browse:email]

header			= Email
width			= "13%"
length = 24

[browse:ip]

header = IP Address
width = "13%"
filter_import = "siteblog.Filters"
filter = "siteblog_filter_ip"

[browse:date]

header			= Posted
width			= "15%"
filter_import = "siteblog.Filters"
filter = "siteblog_filter_nicedate"

[browse:body]

header			= Comment
width			= "18%"
length			= 30

[browse:child_of_post]

header			= Blog Post
width			= "24%"
filter_import = "siteblog.Filters"
filter = "siteblog_filter_blog_link"

; */ ?>