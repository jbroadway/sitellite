; <?php /*

[Collection]

name			= sitellite_sidebar
display			= Sidebars
singular		= Sidebar

; This determines whether versioning is enabled or not for this collection.
is_versioned	= yes

; This determines the display order of the collections in the control panel.
; The higher the number, the closer to the top of the list.  We make the
; sitellite_page number very high, so that it is always on top (logically so),
; but an ordinary default should be between 1 and 10.  Values of 0 will simply
; not be displayed in the list (which is desirable in some circumstances).
list_weight		= 3

key_field		= id

title_field		= title
title_field_name= Box Name

body_field		= body

; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.
add				= form:cms/add/sitellite_sidebar
edit			= form:cms/edit/sitellite_sidebar

; Additional fields to display

[Source]

name			= Database

[Store]

name			= Database

[browse:id]

header			= ID
width			= "15%"

[browse:title]

header			= Page Title

[browse:position]

header			= Position
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
fields			= "title, body"

[facet:sitellite_status]

display			= Status
type			= select
values			= "session_get_statuses ()"

[facet:sitellite_access]

display			= Access Level
type			= select
values			= "session_get_access_levels ()"

[hint:position]

type = hidden

[hint:sorting_weight]

type = hidden

[hint:show_on_pages]

type = hidden

[hint:alias]

type = hidden

[hint:body]

type = xed.Widget.Xeditor

; */ ?>