[Form]

error_mode = all
extra = "enctype=`multipart/form-data`"
uploadFiles = false

[name]
alt = Option Name
type = text
rule 1 = "not empty, You must enter an option name."

[type]

type = selector
alt = Option Group
table			= siteshop_option_type
key				= name
rule 1 = "not empty, You must enter an option group."

[value]

type = text
alt = Extra Info

[image]

alt = "Image (Optional)"
type = file

[weight]
type = text
alt = "Sorting Weight"
rule 1 = "numeric, The weight must be an integer 1-100"
default_value = 5

[submit_button]

type			= msubmit
button 0		= Create
button 1		= "Cancel, onclick=`history.go (-1); return false`"
