; <?php /*

[Collection]

name			= sitellite_undo
display			= Undo
singular		= Item

; This determines the display order of the collections in the control panel.
; The higher the number, the closer to the top of the list.  We make the
; sitellite_page number very high, so that it is always on top (logically so),
; but an ordinary default should be between 1 and 10.  Values of 0 will simply
; not be displayed in the list (which is desirable in some circumstances).
list_weight		= 0

key_field		= id

is_versioned	= yes
visible			= no
scheduler_skip	= yes
translate = no

[Source]

name			= Blank

[Store]

name			= Database

; */ ?>