[Form]

error_mode = all
; extra = "onsubmit=`xed_copy_value (this, 'body')` enctype=`multipart/form-data`"
uploadFiles = false

[image1]

type = file
alt = "Default image (.jpg)"
rule 0 = not empty, You must upload a product image
rule 1 = ext 'jpg', You can only upload jpg images

[submit_button]

type = submit
setValues = Send
