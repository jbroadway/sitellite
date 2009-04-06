; <?php /*

[Form]

error_mode = all
extra = "enctype=`multipart/form-data`"
uploadFiles = false

[return]

type = hidden

[folder]

type = hidden

[file]

type = file
alt = File Upload
rule 0 = not empty, You forgot to upload the file itself.
rule 1 = "func `sitellite_filesystem_rule_extension`, Your file name must have an extension."
rule 2 = "func `sitellite_filesystem_rule_unique`, A file by this name already exists.  Please edit that file to upload a new copy."
extra = "size=`30` id=`file`"

[submit_button]

type = msubmit
button 0 = Upload
button 1 = "Cancel, onclick=`window.history.back (); return false`"

; */ ?>