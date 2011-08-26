[Meta]

name = Embed Wiki

[show]

alt = Start Page
type = select
setValues = "eval: assocify (db_shift_array ('select id from sitewiki_page where id != `` order by id asc'))"
