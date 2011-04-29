; <?php /*

[Collection]

name			= sitellite_filesystem
display			= Web Files
singular		= File
icon			= inc/app/cms/pix/icons/sitellite_filesystem.gif

; This determines the display order of the collections in the control panel.
; The higher the number, the closer to the top of the list.  We make the
; sitellite_page number very high, so that it is always on top (logically so),
; but an ordinary default should be between 1 and 10.  Values of 0 will simply
; not be displayed in the list (which is desirable in some circumstances).
list_weight		= 99

key_field		= name
;key_field_name	= File Name

title_field		= name
title_field_name= File Name

summary_field	= description
keywords_field	= keywords
body_field		= body

; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.
add				= form:cms/add/sitellite_filesystem
edit			= form:cms/edit/sitellite_filesystem
translate		= no

is_versioned	= yes
sitesearch_url	= "cms-filesystem-action?file=%s"
sitesearch_access	= private

[Source]

name			= Filesystem
path			= inc/data

[Store]

name			= Database
binary_fields	= "body"

[browse:name]

header			= File Name
width			= "18%"
length			= 40

[browse:display_title]

header			= Display Name
width			= "18%"
length			= 40

[browse:filesize]

header			= Size
width			= "6%"
filter			= format_filesize

[browse:downloads]

header			= Downloads
filter_import	= cms.Filters
virtual			= cms_virtual_filesystem_download
width			= "10%"
align			= center

[browse:last_modified]

header			= Last Modified
filter			= cms_filter_date_time
filter_import	= cms.Filters
width			= "20%"

[browse:sitellite_status]

header			= Status
width			= "8%"
filter			= ucwords

[browse:sitellite_access]

header			= Access
width			= "8%"
filter			= ucwords

[facet:name]

display			= Folder
type			= folder
values			= "db_shift_array ('select distinct path from sitellite_filesystem order by path asc')"

[facet:keywords]

display			= Text
type			= text
fields			= "name, display_title, keywords, description"

[facet:extension]

display			= File Type
type			= filetype
values			= "db_shift_array ('select distinct extension from sitellite_filesystem order by extension asc')"

[facet:sitellite_status]

display			= Status
type			= select
values			= "session_get_statuses ()"

[facet:sitellite_access]

display			= Access Level
type			= select
values			= "session_get_access_levels ()"

; */ ?>
