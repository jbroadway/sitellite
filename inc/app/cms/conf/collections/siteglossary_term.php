; <?php /*

[Collection]

name			= siteglossary_term
app				= siteglossary
display			= Glossary
singular		= Term
key_field		= word
title_field		= word
title_field_name= Term
summary_field	= description
body_field		= description

sitesearch_url			= "siteglossary-app#%s"
sitesearch_access		= public
sitesearch_highlight	= no

add = siteglossary/add
edit = siteglossary/edit

; Additional fields to display

[Source]

name			= Database

[Store]

name			= Blank

[browse:word]

header			= Term

[browse:category]

header			= Category

[browse:description]

header			= Summary
length			= 40

[browse:body]

header			= Definition
length			= 80
filter			= strip_tags

[facet:word]

display			= Text
type			= text
fields			= "word, description, body"

[facet:category]

display			= Category
type			= select
values			= "db_shift_array ('select name from siteglossary_category order by name asc')"

; */ ?>