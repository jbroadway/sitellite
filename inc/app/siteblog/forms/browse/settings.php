; <?php /*

[Form]

name            = browse
description     = browse posts
author          = You <you@yourWebSite.com>
copyright       = "Copyright (C) 2004, Me Inc."
license         = "http://www.opensource.org/licenses/index.php#PICK ONE!!!"
version         = 0.1

; your form definition goes here

[author]

type = select

[year]

type = select

[month]

type = select
rule 1 = "func 'siteblog_rule_year', You must specify a year with month."

[category]

type = select

[submit]

type = submit
setValues = Search

; */ ?>
