[Form]

error_mode = all
title = Edit Item

[id]

type = hidden

[pp]

type = hidden

[proj]

type = hidden

[todo]

alt = To Do
type = text
extra = "size='50'"

[priority]

type = select
setValues = "eval: assocify (array ('normal','high','urgent'))"

[project]

type = selector
table = todo_project
key = name
addAction = todo/selector/add
removeAction = todo/selector/remove

[person]

type = selector
table = todo_person
key = name
addAction = todo/selector/add
removeAction = todo/selector/remove

[done]

type = checkbox
value 1 = "Done?"
fieldset = no

[submit_button]

type = submit
setValues = "Save"

