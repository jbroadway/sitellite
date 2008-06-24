; <?php /*

[Collection]

name			= siteforum_post
app				= siteforum
display			= Forum
singular		= Post

; This determines the display order of the collections in the control panel.
; The higher the number, the closer to the top of the list.  We make the
; sitellite_page number very high, so that it is always on top (logically so),
; but an ordinary default should be between 1 and 10.  Values of 0 will simply
; not be displayed in the list (which is desirable in some circumstances).
list_weight		= 100

key_field		= id
key_field_name	= ID

title_field		= subject
title_field_name= Subject

body_field		= body

; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.
;add				= form:cms/add/sitellite_page
;edit			= form:cms/edit/sitellite_page

is_versioned	= no
sitesearch_url		= "siteforum-post-action/id.%s"
sitesearch_access	= public
visible			= no
scheduler_skip	= yes

[Source]

name			= Database

[Store]

name			= Blank

; */ ?>
