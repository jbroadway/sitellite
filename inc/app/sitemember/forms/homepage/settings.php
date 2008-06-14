; <?php /*

[Form]

error_mode = all
extra = "onsubmit=`xed_copy_value (this, 'body')`"

[title]

type = text
alt = Page Title
extra = "size=`40`"

[template]

;type = select
;alt = Display Template
;setValues = "eval: array ('' => 'Default')"
type = hidden
setValue = ""

[body]

type = xed.Widget.Xeditor

[submit_button]

type = msubmit
button 1 = Save
button 2 = "Cancel, onclick=`history.go (-1); return false`"

; */ ?>