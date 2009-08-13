; <?php /*

[Meta]

name            = list-tags
description     = Returns the list of tags associated with this page.
author          = Charles Brunet <charles@cbrunet.net>
copyright       = "Copyright (C) 2009, Charles Brunet."
version         = 0.1

[set]

type = select
alt = Tag Set
setValues = "eval: loader_call ('sitetag.Filters', 'getTagSetList')"

[separator]

type = text
alt = Separator
default_value = " "

; */ ?>
