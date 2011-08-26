; <?php /*

[Meta]

name = Full Directory
description = The main SiteLinks directory index.

[category]

type = select
alt = Category
setValues	= "eval: array_merge (array ('' => '- SELECT -'), assocify (db_shift_array ('select distinct id from sitelinks_category where id != `` order by id asc')))"

; */ ?>