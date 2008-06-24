; <?php /*

[Form]

name            = submission
description     = User-contributed FAQ submission form
author          = Lux <lux@simian.ca>
copyright       = "Copyright (C) 2004, SIMIAN systems Inc."
license         = "http://www.opensource.org/licenses/gpl-license.php"
version         = 0.1

; your form definition goes here

error_mode		= all

[name]

type			= text
alt				= Your Name

[email]

type			= text
alt				= Email Address
rule 1			= not empty, You must enter your email address in order to receive a response.

[url]

type			= text
alt				= Web Site
default_value	= "http://"

[age]

type			= select
setValues		= "eval: appconf ('user_age_list')"

[question]

type			= textarea
rule 1			= not empty, You forgot to enter your question.

[submit_button]

type			= submit
setValues		= Send

; */ ?>