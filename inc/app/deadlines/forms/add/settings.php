[Form]

error_mode = all

[title]

type = text

[project]

type = selector
table = deadlines_project
key = name

[type]

type = select
setValues = "eval: assocify (array ('beta','deadline','meeting','milestone','report'))"
setDefault = deadline

[ts]

alt = Date/Time
type = datetimeinterval

[details]

type = textarea
labelPosition = left
rows = 10

[submit_button]

type = msubmit
button 1 = Create
button 2 = "Cancel, onclick=`window.location.href = '/index/deadlines-app'; return false`"
