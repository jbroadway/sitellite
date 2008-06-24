; <?php /*

[Form]

error_mode	= all

[id]

type		= hidden

[subject]

type		= text
extra		= "size=`40`"
rule 1		= not empty, You must enter a subject line.

[body]

type		= siteforum.Widget.Forumbox
rows		= 15
cols		= 50
alt		= "Message (allowed HTML tags: strong, em, a, blockquote, code)"
rule 1		= not empty, You must enter a message.
extra		= "id=`siteforum-body`"

[submit_button]

type		= msubmit
button 1	= Save
button 2	= Preview
button 3	= Cancel

; */ ?>