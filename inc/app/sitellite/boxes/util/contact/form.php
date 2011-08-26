; resolved tickets:
; #171 nolist error_mode

[Form]

;Start: SEMIAS #171 nolist error_mode.
error_mode = nolist
;END: SEMIAS
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

;Start: SEMIAS #188 form captcha improvements.
[security]

alt = Security
type = security
verify_method = figlet
;END: SEMIAS

[submit_button]

type = submit
setValues = Send
