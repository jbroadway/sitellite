; <?php /*

[Form]

error_mode = all

[wiki_name]

type = text
alt = Wiki Name

[wiki_description]

type = text
alt = Wiki Description
extra = "size=`50`"

[language]

type = text
alt = Language
extra = "size=`10`"

[template]

type = cms.Widget.Templates
alt = Display with Template

[security_test]

type = select
alt = Security Test
setValues = "eval: appconf ('yesno')"

[default_view_level]

type = select
alt = Default View Level
setValues = "eval: appconf ('levels')"

[default_edit_level]

type = select
alt = Default Edit Level
setValues = "eval: appconf ('levels')"

[minimum_edit_level]

type = select
alt = Minimum Edit Level
setValues = "eval: appconf ('levels')"

[allowed_file_types]

type = textarea
alt = Allowed File Types
labelPosition = left

[submit_button]

type = msubmit
button 1 = Save
button 2 = "Cancel, onclick=`window.location.href = 'sitewiki-app'; return false`"

; */ ?>