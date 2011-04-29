; <?php /*

[Form]

name            = editset
description     = Template Set Editor
author          = You <you@yourWebSite.com>
copyright       = "Copyright (C) 2004, Me Inc."
license         = "http://www.opensource.org/licenses/index.php#PICK ONE!!!"
version         = 0.1

; your form definition goes here

[set]

type			= hidden

[set_name]

type			= text
extra			= "size=`50`"
rule 1                  = "regex `^[-a-zA-Z0-9_. ]+$`, Set names only can contain alphanumeric values and spaces."

[description]		

type 			= text
extra			= "size=`50`"

[author]
			
type			= text
extra			= "size=`50`"

[copyright]		

type			= text
extra			= "size=`50`"

[license]
			
type 			= text
extra			= "size=`50`"

[version]
 
type 			= text
extra			= "size=`50`"

[modes]

type			= textarea
cols 			= 60

[submit_button]

type			= msubmit
button 1		= Save
button 2		= Cancel

; */ ?>
