[Form]

method = post
error_mode = all
uploadFiles = false

[src]

type = hidden

[location]

type = hidden

[new_file]

type = file
alt = Find the file
rule 1 = "not empty, You must choose an image file."

[submit_button]

type = msubmit
setValues = Save
button 0 = Save
button 1 = Cancel
