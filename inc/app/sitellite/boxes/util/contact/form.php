[Form]

error_mode = all
verify_sender = yes
clean_input = yes

[name]

alt = Your Name
type = text
rule 1 = "header, Your name contains invalid characters."
extra = "maxlength=`72`"

;[email]

;type = hidden

[from]

alt = Email Address
type = text
rule 1 = "email, Your email address does not appear to be valid."
extra = "maxlength=`72`"

[message]

alt = Comments/Questions
type = textarea
;labelPosition = left

[submit_button]

type = submit
setValues = Send
