; <?php /*

[Form]

[name]

type		= text
rule 1		= not empty, You must enter a name for your folder.
rule 2		= "not is `Inbox`, The name you have chosen already exists."
rule 3		= "not is `Sent`, The name you have chosen already exists."

[submit_button]

type		= msubmit
button 1	= "Save"
button 2	= "Cancel, onclick=`window.location.href = 'cms-messages-action'; return false`"

; */ ?>