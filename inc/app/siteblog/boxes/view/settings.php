; <?php /*

[Meta]

name            = SiteBlog
description     = Embed a blog in your page
 
[Category]
 
type = select
alt = Category
setValues = "eval: loader_call ('siteblog.Functions', 'SiteBlog::categories')"

[limit]

type = select
alt = Limit the number of Posts to Display
setValues = "eval: assocify (array (1, 2, 3, 4, 5, 10, 20))"
setDefault = 10

; */ ?>
