; <?php /*

[Form]

error_mode		= all
extra           = "onsubmit=`xed_copy_value (this, 'body')`"

[collection]

type	= hidden

[_collection]

type	= hidden

[_key]

type	= hidden

[title]

type = hidden

[status]

type    = hidden

[complete]

type    = hidden

[from_name]

alt		= From Name
type    = text
size		= 40

[from_email]

alt		= From Email
type    = text
size		= 40
rule 1 = "not empty, You must specify a reply email address."

[template]

alt			= Template
type		= select
setValues	= "eval: array ('' => '- SELECT -') + db_pairs ('select id,title from sitemailer2_template order by title asc')"
rule 1 = "not empty, You must specify a template."

[recur]

type        = select
alt         = Re-occurring
    
[date]

type			= calendar
data_value		= SITEEVENT_TODAY
rule 1 = "not empty, You must set a day and time. If you set the message for a time in the past, it will have a priority over messages set for a later date and time."


[time]

type			= select
setValues		= "eval: formdata_get ('hours')"
extra			= "id=`time` style=`font-family: monospace; padding-top: 2px`"
rule 1 = "not empty, You must set a day and time. If you set the message for a time in the past, it will have a priority over messages set for a later date and time."

;newsletter
[newsletter]

alt			= Newsletters
;type        = selector
type		= multiple
size        = 5
;table       = sitemailer2_newsletter
setValues   = "eval: db_pairs ('select id, name from sitemailer2_newsletter')"
;extra       = "onfocus=`formhelp_show(this,'blah')` onblur=`formhelp_hide()`"
;key         = id
;title       = name

[subject]

alt			= Subject
type        = text
size		= 40

[body]

type = xed.Widget.Xeditor
; clean = off

[test_email]

type = hidden

[submit_button]

type     = msubmit
button 1 = Send Message
button 2 = Save as Draft
button 3 = Stop Sending
button 4 = Send Test Message
button 5 = Cancel

; */ ?>
