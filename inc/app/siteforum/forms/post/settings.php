; <?php /*

[Form]

error_mode	= all
uploadFiles = no

[topic]

type		= hidden

[post]

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

[attachment]

type		= file
alt			= Attach a file to your post.

[subscribe]

type		= checkbox
value 1		= Subscribe me to this forum thread.
fieldset	= off

[notice]

type		= checkbox
value 1		= Make this post a notice.
fieldset	= off

[submit_button]

type		= msubmit
button 1	= Post
button 2	= Preview
button 3	= Cancel

; */ ?>