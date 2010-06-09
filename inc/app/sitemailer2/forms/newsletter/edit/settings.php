[Form]

[id]

type = hidden

[name]

type = text
alt = Newsletter Name

[from_name]

type = text
alt = From Name

[from_email]

type = text
alt = From Email

[template]

type = select

[subject]

type = text
alt = Subject

[public]

type = select
alt = Public
setValues = "eval: assocify (array ('yes', 'no'))"

[submit_button]

type = msubmit
button 1 = Save
button 2 = Cancel
