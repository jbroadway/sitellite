; <?php /*

[Form]

uploadFiles = no
extra = "enctype=`multipart/form-data`"

[instructions]

type = template
template = instructions.spt

[page]

type = hidden

[editing]

type = hidden
setValue = "true"

[body]

alt = ""
type = textarea
rows = 32
cols = 55

[view_level]

type = select
alt = Viewable by
setValues = "eval: appconf ('levels')"

[edit_level]

type = select
alt = Editable by
setValues = "eval: appconf ('levels')"

[files]

type = section
title = Attach Files

[file_1]

type = file

[file_2]

type = file

[file_3]

type = file

[security_test]

type = security

[submit_button]

type = msubmit
button 1 = Save
button 2 = Cancel

; */ ?>