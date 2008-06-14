; <?php /*

[Form]

message			= Please use this form to submit stories for possible inclusion on our site.
verify_sender	= yes

[title]

alt				= Title
type			= text
rule 1			= not empty, You must enter a title.

[author]

alt				= Author
type			= text
rule 1			= not empty, You must enter an author name.

[category]

type			= select
alt				= Section

[summary]

alt				= Summary
type			= textarea
cols			= 50
rows			= 3
rule 1			= not empty, You must enter a summary.

[body]

type			= textarea
alt				= "Body (XHTML format, use <hr /> to separate multiple pages)"
cols			= 50
rows			= 20
rule 1			= not empty, You must enter a body.

[security_test]

type			= security

[submitButton]

type			= submit
setValues		= Submit

; */ ?>