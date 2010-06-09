; <?php /*

[Form]

extra		= "enctype=`multipart/form-data`"

[file_csv]

type		    = file
alt		    = Comma Separated Values File

[_key]

type        = hidden

[_collection]

type        = hidden

[collection]

type        = hidden

[newsletter]

alt        = Newsletters to Unsubscribe Users From
type        = multiple
size        = 5
multiple    = true
;table       = sitemailer2_newsletter
;key         = id
;title       = name
setValues	= "eval: db_pairs ('select id, name from sitemailer2_newsletter order by name asc')"
rule 1		= "not empty, You must select at least one Newsletter for your subscriber."

[submit_button]

type		    = msubmit
button 0 	= "Next"
button 1 	= "Cancel, onclick=`window.location.href = 'sitemailer2-subscribers-action'; return false`"

; */ ?>