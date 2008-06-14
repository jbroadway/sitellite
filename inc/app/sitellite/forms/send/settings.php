[Form]

method = post
error_mode = all
message = "Please note: If there are many recipients, it may take some time to send them all. Please be patient and let the page finish loading after clicking Submit."

[from_email]

type = text
alt = From Email
rule 1 = not empty, You must enter a From Email.

[from_name]

type = text
alt = From Name
rule 1 = not empty, You must enter a From Name.

[subject]

type = text
alt = Subject
rule 1 = not empty, You must enter a Subject.

[send_to]

type = select
alt = Send to Group

[include_no_consent]

type = checkbox
fieldset = no
value 1 = "Include recipients who have not been asked consent (may be against spam/privacy laws in some countries)"

[message]

type = textarea
alt = "Message (no html)"
cols = 80
rows = 20
rule 1 = not empty, You must enter a Message.

[submit_button]

type = submit
setValues = Submit
extra = "onclick=`this.value = 'Please wait...'`"
