[Form]

method = post
message = "Export contacts from the specified group in CSV (Excel-compatible) format. <a href='#' onclick='history.go (-1)'>Back</a>"

[group]

type = select
alt = Send to Group

[submit_button]

type = submit
setValues = Export
extra = "onclick=`this.value = 'Please wait...'`"
