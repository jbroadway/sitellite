; <?php /*

[Collection]

name			= sitefaq_question
app				= sitefaq
display			= FAQs
singular		= Question
key_field		= id
title_field		= question
title_field_name= Question
summary_field	= answer
body_field		= answer

sitesearch_url			= "sitefaq-app#faq-%s"
sitesearch_access		= public
sitesearch_highlight	= no

; Additional fields to display

[Source]

name			= Database

[Store]

name			= Blank

[browse:question]

header			= Question
length			= 100

[browse:category]

header			= Category
length          = 40

[browse:answer]

header			= Answer
width   		= "30%"
filter			= strip_tags

[hint:question]

alt				= Question
type			= textarea
rows			= 2
labelPosition	= left

[hint:category]

type			= selector
table			= sitefaq_category
key				= name

[hint:answer]

type			= xed.Widget.Xeditor
alt				= Answer
;rows			= 8
;labelPosition	= left

[facet:question]

display			= Text
type			= text
fields			= "question, answer"

[facet:category]

display			= Category
type			= select
values			= "db_shift_array ('select name from sitefaq_category order by name asc')"

; */ ?>