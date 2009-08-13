; <?php /*

[Meta]

name            = list-items
description     = Get the list of pages associated with a given tag.
author          = Charles Brunet <charles@cbrunet.net>
copyright       = "Copyright (C) 2009, Charles Brunet."
version         = 0.1

[set]

type = select
alt = Tag Set
setValues = "eval: loader_call ('sitetag.Filters', 'getTagSetList')"

; */ ?>
