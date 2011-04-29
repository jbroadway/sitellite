; <?php /*

[Form]

name            = edit
description     = Add or edit tags for the current page
author          = Charles Brunet <charles@cbrunet.net>
copyright       = "Copyright (C) 2009, Charles Brunet."
version         = 0.1

error_mode      = all
formhelp        = on

[set]

type            = hidden

[url]

type            = info

[title]

type            = text
rule 1          = "not empty, You must specify a title."
formhelp        = "This is the title of the url you want to tag. This title will be displayed to identify the page."

[description]

type            = textarea
formhelp        = "This is an optional description for the page you want to tag."

[tags]

; A custom javascript widget should be created for that
type            = sitetag.Widget.taginput
formhelp        = "Space separated list of tags. Use underscores to join many words."

[submit_button]

type            = msubmit
button 0        = Save
button 1        = Cancel

; */ ?>
