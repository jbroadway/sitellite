[Form]

title = Edit HTML Source
extra = "id='source-form'"

[ifname]

type = hidden

[controls]

type = template
template = source_controls.spt

[html]

type = textarea
alt = ""
rows = 22
cols = 78
extra = "id='html' wrap='soft'"

[submit_button]

type = msubmit
button 1 = OK
button 2 = "Cancel, onclick='window.close (); return false'"
