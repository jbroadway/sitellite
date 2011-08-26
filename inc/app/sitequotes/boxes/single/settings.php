[Meta]

name = Single Quote
description = "Place a single selected quote on your web site."

[id]

type = select
alt = "Quote"
setValues = "eval: db_pairs (`select id, concat(person, ': ', substring(quote, 1, 20), '...') as q from sitequotes_entry order by q asc`)"
