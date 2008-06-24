[Form]

error_mode = all
title = Add Items

[todo]

alt = "To Do (one per line)"
type = textarea
rows = 10
cols = 50
labelPosition = left

[priority]

type = select
setValues = "eval: assocify (array ('normal','high','urgent'))"

[proj]

alt = Project
type = selector
table = todo_project
key = name
addAction = todo/selector/add
removeAction = todo/selector/remove

[pp]

alt = Person
type = selector
table = todo_person
key = name
addAction = todo/selector/add
removeAction = todo/selector/remove

[submit_button]

type = submit
setValues = "Add"
